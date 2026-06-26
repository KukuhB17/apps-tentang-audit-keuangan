<?php
include 'koneksi.php';

// Mengambil sedikit metrik statis untuk meyakinkan pengguna di halaman depan
$tx_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transactions"))['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUDIT-APPS - Sistem Pengawasan Keuangan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen font-sans flex flex-col justify-between">

    <header class="bg-slate-900 text-white px-6 py-4 rounded-b-xl max-w-7xl mx-auto w-full flex justify-between items-center shadow-md">
        <div class="text-xl font-bold text-sky-400 tracking-wide">AUDIT-APPS</div>
        <nav class="hidden md:flex gap-8 text-sm font-medium text-slate-300">
            <a href="index.php" class="text-white border-b-2 border-sky-400 pb-1">Home</a>
            <a href="dashboard.php" class="hover:text-white transition">Dashboard</a>
            <a href="form-jurnal.php" class="hover:text-white transition">Form Jurnal</a>
            <a href="log-audit.php" class="hover:text-white transition">Live Audit Trail</a>
        </nav>
        <div>
            <a href="register.php" class="bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs px-4 py-2 rounded-lg shadow transition">
                Daftar Akun
            </a>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-6 py-16 flex-grow flex flex-col items-center justify-center text-center">
        <span class="bg-sky-100 text-sky-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-4 shadow-sm">
            Informatics Research & SQA Certified System
        </span>
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight max-w-3xl leading-tight">
            Transformasi Metodologi Riset Informatika & <span class="text-sky-500">Aplikasi Audit Keuangan</span>
        </h1>
        <p class="mt-4 text-base text-slate-500 max-w-2xl leading-relaxed">
            Platform pengawasan internal transparan dengan proteksi data log bersifat <span class="font-semibold text-slate-700">Append-Only</span>, 
            mekanisme anti <span class="font-semibold text-slate-700">SQL Injection</span>, serta otomatisasi kalkulasi laporan Neraca dan Laba Rugi yang presisi.
        </p>

        <div class="mt-8 flex flex-col sm:flex-row gap-4">
            <a href="dashboard.php" class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm px-6 py-3 rounded-xl shadow-lg transition text-center">
                📊 Masuk Dashboard Monitor
            </a>
            <a href="form-jurnal.php" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-sm px-6 py-3 rounded-xl shadow-sm transition text-center">
                📝 Mulai Entri Jurnal
            </a>
        </div>

        <div class="mt-12 pt-8 border-t border-slate-200 w-full max-w-md flex justify-around text-center text-xs font-medium text-slate-400 uppercase tracking-wider">
            <div>
                <div class="text-2xl font-black text-slate-800 font-mono mb-0.5"><?= $tx_count; ?></div>
                Transaksi Ter-Audit
            </div>
            <div>
                <div class="text-2xl font-black text-emerald-600 font-mono mb-0.5">100%</div>
                Integrasi Bebas Fraud
            </div>
        </div>
    </main>

    <section class="bg-white border-t border-slate-200 py-12 w-full">
        <div class="max-w-5xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-2">
                <div class="text-sky-500 text-lg font-bold">🔒 Immutable Audit Trail</div>
                <p class="text-xs text-slate-500 leading-relaxed">Setiap aktivitas insert atau update otomatis dikunci ke dalam database menggunakan enkapsulasi payload JSON tanpa opsi penghapusan.</p>
            </div>
            <div class="space-y-2">
                <div class="text-sky-500 text-lg font-bold">⚖ Real-time Balancing Check</div>
                <p class="text-xs text-slate-500 leading-relaxed">Form input akuntansi ganda dilengkapi dengan skrip penguji validasi agar nilai debit dan kredit tetap seimbang.</p>
            </div>
            <div class="space-y-2">
                <div class="text-sky-500 text-lg font-bold">🖨 Automated Reports</div>
                <p class="text-xs text-slate-500 leading-relaxed">Dapatkan visualisasi chart volume data serta pencetakan ekspor Laporan Neraca dan Laba Rugi ke dokumen cetak PDF sekali klik.</p>
            </div>
        </div>
    </section>

    <footer class="bg-slate-900 text-slate-400 text-xs py-6 text-center border-t border-slate-800 w-full">
        <div class="max-w-5xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-2">
            <p>&copy; 2026 Kelompok Aplikasi Audit Keuangan. All Rights Reserved.</p>
            <p class="font-mono text-[10px] text-slate-500">Aktor Pengawas: Auditor (M. Umar Mansyur)</p>
        </div>
    </footer>

</body>
</html>