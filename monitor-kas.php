<?php
include 'middleware-auth.php';
// Auditor saja yang boleh melihat monitor kas
batasiAksesKe('Auditor');
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Monitor Kas - AUDIT-APPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen p-6 font-sans">
    <?php include 'navbar.php'; ?>

    <div class="max-w-6xl mx-auto bg-white p-8 rounded-xl shadow-sm border mt-4">
        <div class="border-b pb-4 mb-6">
            <h1 class="text-xl font-bold text-slate-900">🔍 Monitor Kas Internal</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar transaksi kas dan status kelengkapan bukti fisik.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 font-bold border-b">
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Deskripsi</th>
                        <th class="p-3 text-right">Nominal</th>
                        <th class="p-3 text-center">Status Bukti</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php
                    // Query Left Join untuk menarik semua transaksi kas
                    // Kode '101' adalah contoh kode akun Kas
                    $query = "SELECT t.transaction_id, t.transaction_date, t.description, td.amount, p.file_name 
                              FROM transactions t
                              JOIN transaction_details td ON t.transaction_id = td.transaction_id
                              LEFT JOIN proofs p ON t.transaction_id = p.transaction_id
                              WHERE td.account_code = '101' 
                              ORDER BY t.transaction_date DESC";
                    
                    $result = mysqli_query($conn, $query);

                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            $is_verified = !empty($row['file_name']);
                    ?>
                    <tr class="hover:bg-slate-50 <?= !$is_verified ? 'bg-rose-50' : '' ?>">
                        <td class="p-3"><?= date('d M Y', strtotime($row['transaction_date'])) ?></td>
                        <td class="p-3"><?= htmlspecialchars($row['description']) ?></td>
                        <td class="p-3 text-right font-mono">Rp <?= number_format($row['amount'], 0, ',', '.') ?></td>
                        <td class="p-3 text-center">
                            <?php if($is_verified): ?>
                                <span class="bg-emerald-100 text-emerald-800 px-2 py-1 rounded font-bold">✅ LENGKAP</span>
                            <?php else: ?>
                                <span class="bg-rose-100 text-rose-800 px-2 py-1 rounded font-bold">❌ BELUM ADA</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='4' class='p-6 text-center text-slate-400'>Tidak ada data transaksi kas.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>