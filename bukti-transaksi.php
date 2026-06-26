<?php
include 'middleware-auth.php';
cekAutentikasi(); // Semua yang login bisa melihat daftar bukti
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Bukti - AUDIT-APPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen p-6 font-sans">

    <?php include 'navbar.php'; ?>

    <div class="max-w-6xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-slate-200 mt-4">
        <div class="border-b pb-4 mb-6">
            <h1 class="text-xl font-bold text-slate-900">📚 Daftar Bukti Fisik Transaksi</h1>
            <p class="text-xs text-slate-500 mt-1">Rekapitulasi dokumen pendukung yang telah diunggah ke sistem.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-bold border-b">
                        <th class="p-3">ID Transaksi</th>
                        <th class="p-3">Nama File</th>
                        <th class="p-3">Pengunggah</th>
                        <th class="p-3">Waktu Upload</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php
                    // Join table proofs dengan users untuk mendapatkan nama pengunggah
                    $query = "SELECT p.*, u.full_name FROM proofs p 
                              JOIN users u ON p.uploaded_by = u.user_id 
                              ORDER BY p.created_at DESC";
                    $result = mysqli_query($conn, $query);

                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr class="hover:bg-slate-50">
                        <td class="p-3 font-mono font-bold text-sky-600">TX-<?= $row['transaction_id'] ?></td>
                        <td class="p-3"><?= htmlspecialchars($row['file_name']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($row['full_name']) ?></td>
                        <td class="p-3"><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                        <td class="p-3">
                            <a href="<?= $row['file_path'] ?>" target="_blank" class="bg-slate-900 text-white px-3 py-1 rounded text-[10px] font-bold hover:bg-slate-700">Lihat File</a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='5' class='p-6 text-center text-slate-400 italic'>Belum ada bukti yang diunggah.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>