<?php
include 'middleware-auth.php';
include 'koneksi.php';

// Proses Hapus (Hanya Auditor)
if (isset($_GET['hapus'])) {
    batasiAksesKe('Auditor');
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM coa WHERE id = $id");
    header("Location: coa.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Master COA - AUDIT-APPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 p-6">
    <?php include 'navbar.php'; ?>
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl border border-slate-200">
        <div class="flex justify-between mb-4">
            <h2 class="font-bold text-lg">Master Data Kode Akun (COA)</h2>
            <?php if($_SESSION['role'] == 'Auditor'): ?>
                <a href="coa-form.php" class="bg-sky-600 text-white px-4 py-2 rounded text-xs font-bold">+ Tambah COA</a>
            <?php endif; ?>
        </div>
        <table class="w-full text-left text-xs">
            <tr class="bg-slate-100 uppercase font-bold text-slate-500">
                <th class="p-2">Kode</th><th class="p-2">Nama Akun</th><th class="p-2">Tipe</th>
                <?php if($_SESSION['role'] == 'Auditor'): ?><th class="p-2">Aksi</th><?php endif; ?>
            </tr>
            <?php
            $q = mysqli_query($conn, "SELECT * FROM coa ORDER BY account_code ASC");
            while($r = mysqli_fetch_assoc($q)): ?>
            <tr class="border-b">
                <td class="p-2 font-mono"><?= $r['account_code'] ?></td>
                <td class="p-2"><?= $r['account_name'] ?></td>
                <td class="p-2"><?= $r['account_type'] ?></td>
                <?php if($_SESSION['role'] == 'Auditor'): ?>
                <td class="p-2">
                    <a href="coa-form.php?id=<?= $r['id'] ?>" class="text-amber-600 mr-2">Edit</a>
                    <a href="?hapus=<?= $r['id'] ?>" class="text-rose-600" onclick="return confirm('Yakin?')">Hapus</a>
                </td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>