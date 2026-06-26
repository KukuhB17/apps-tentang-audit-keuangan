<?php
// Panggil proteksi session dasar
include 'cek-session.php';

// Proteksi spesifik: Jika yang masuk bukan Klien (misal Auditor menyusup), tolak!
if ($_SESSION['role'] !== 'Klien') {
    echo "<script>alert('Akses Ilegal! Halaman ini dikunci khusus untuk Klien.'); window.location='login.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Klien - AUDIT-APPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen p-6 font-sans flex flex-col justify-between">

    <?php include 'navbar.php'; ?>

    <div class="flex-grow flex items-center justify-center my-8">
        <div class="max-w-4xl w-full bg-white p-8 rounded-xl shadow-sm border border-slate-200">
            <div class="border-b pb-4 mb-6">
                <h1 class="text-2xl font-bold text-slate-900">Panel Pengisian Jurnal (Klien)</h1>
                <p class="text-xs text-slate-500 mt-1">Selamat datang, <strong><?= htmlspecialchars($_SESSION['full_name']); ?></strong></p>
            </div>
            
            <div class="bg-amber-50 border border-amber-200 p-4 rounded-lg mb-6 text-xs text-amber-800 font-medium">
                📋 **Privilese Terbatas Operator:** Anda hanya memiliki hak akses untuk memasukkan data transaksi baru.
            </div>

            <div class="border border-slate-200 p-6 rounded-xl text-center max-w-md mx-auto bg-slate-50">
                <h3 class="font-bold text-sm text-slate-800 mb-2">Input Transaksi Mutasi Baru</h3>
                <a href="form-jurnal.php" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-2.5 px-6 rounded-lg transition inline-block uppercase tracking-wider shadow">
                    ➕ Buka Form Entri Jurnal
                </a>
            </div>
        </div>
    </div>

    <footer class="bg-slate-900 text-slate-400 text-[11px] py-4 text-center border-t border-slate-800 w-full rounded-t-xl">
        <p>&copy; 2026 Kelompok Aplikasi Audit Keuangan. Session Hijacking Protected.</p>
    </footer>

</body>
</html>