<?php
include 'middleware-auth.php';
cekAutentikasi();
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Buku Besar - AUDIT-APPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen p-6 font-sans">
    <?php include 'navbar.php'; ?>

    <div class="max-w-6xl mx-auto bg-white p-8 rounded-xl shadow-sm border mt-4">
        <h2 class="text-lg font-bold text-slate-900 border-b pb-4 mb-6">📊 Laporan Buku Besar Otomatis</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b text-slate-600 font-bold">
                        <th class="p-3">Kode Akun</th>
                        <th class="p-3">Nama Akun</th>
                        <th class="p-3 text-right">Total Debit</th>
                        <th class="p-3 text-right">Total Kredit</th>
                        <th class="p-3 text-right">Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php
                    // Query pengelompokan berdasarkan kode akun
                    $query = "SELECT account_code, account_name, 
                              SUM(CASE WHEN position = 'Debit' THEN amount ELSE 0 END) as total_debit,
                              SUM(CASE WHEN position = 'Kredit' THEN amount ELSE 0 END) as total_kredit
                              FROM transaction_details 
                              GROUP BY account_code, account_name";
                    
                    $result = mysqli_query($conn, $query);
                    while($row = mysqli_fetch_assoc($result)):
                        $saldo = $row['total_debit'] - $row['total_kredit'];
                    ?>
                    <tr class="hover:bg-slate-50">
                        <td class="p-3 font-mono font-bold text-slate-900"><?= $row['account_code'] ?></td>
                        <td class="p-3"><?= $row['account_name'] ?></td>
                        <td class="p-3 text-right text-sky-600">Rp <?= number_format($row['total_debit'], 0, ',', '.') ?></td>
                        <td class="p-3 text-right text-rose-600">Rp <?= number_format($row['total_kredit'], 0, ',', '.') ?></td>
                        <td class="p-3 text-right font-bold text-slate-900">Rp <?= number_format($saldo, 0, ',', '.') ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>