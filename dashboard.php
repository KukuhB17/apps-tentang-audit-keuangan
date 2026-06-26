<?php
include 'koneksi.php';

// Menghitung ribuan data secara cepat memakai metode agregasi basis data terindeks
$tx_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transactions"))['total'];
$log_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM audit_trails"))['total'];

$d_sum = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as t FROM transaction_details WHERE position='Debit'"))['t'] ?? 0;
$k_sum = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as t FROM transaction_details WHERE position='Kredit'"))['t'] ?? 0;
$system_integrity = ($d_sum == $k_sum && $tx_count > 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>AUDIT-APPS - Dashboard Monitor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen p-6 font-sans">

    <nav class="bg-slate-900 text-white px-6 py-4 rounded-xl flex justify-between items-center shadow mb-8">
        <div class="text-xl font-bold text-sky-400 tracking-wide">AUDIT-APPS</div>
        <div class="flex gap-6 text-sm font-medium text-slate-300">
            <a href="dashboard.php" class="text-white border-b-2 border-sky-400 pb-1">Dashboard Monitor</a>
            <a href="form-jurnal.php" class="hover:text-white transition">Form Jurnal</a>
            <a href="log-audit.php" class="hover:text-white transition">Live Audit Trail</a>
            <a href="lembar-kerja.php" class="hover:text-white transition">Lembar Kerja</a>
            <a href="laporan.php" class="hover:text-white transition">Laporan Final</a>
        </div>
        <div class="text-xs text-slate-400">Aktor: <span class="text-white font-bold">M. Umar Mansyur</span></div>
    </nav>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Jurnal Transaksi</div>
            <div class="text-3xl font-extrabold text-slate-800"><?= $tx_count; ?> <span class="text-xs font-normal text-slate-400">Baris</span></div>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm border-l-4 <?= $system_integrity ? 'border-emerald-500' : 'border-rose-500'; ?>">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Status Integritas Jurnal</div>
            <div>
                <?php if($system_integrity): ?>
                    <span class="bg-emerald-100 text-emerald-800 text-[11px] font-bold px-2.5 py-1 rounded">✓ 100% BALANCE MATCH</span>
                <?php else: ?>
                    <span class="bg-rose-100 text-rose-800 text-[11px] font-bold px-2.5 py-1 rounded">⚠ SELISIH (UNBALANCE)</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Log Terkunci</div>
            <div class="text-3xl font-extrabold text-slate-800"><?= $log_count; ?> <span class="text-xs font-normal text-slate-400">Log Keamanan</span></div>
        </div>
    </section>

    <main class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm lg:col-span-8">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b pb-3 mb-6">Grafik Tren Volume Transaksi Bulanan (Tahun 2026)</h3>
            <div class="h-56 flex items-end justify-between px-6 border-b pb-2">
                <div class="w-12 bg-slate-200 rounded-t h-[35%] hover:bg-sky-400 transition" title="Jan"></div>
                <div class="w-12 bg-slate-200 rounded-t h-[55%] hover:bg-sky-400 transition" title="Feb"></div>
                <div class="w-12 bg-slate-200 rounded-t h-[25%] hover:bg-sky-400 transition" title="Mar"></div>
                <div class="w-12 bg-slate-200 rounded-t h-[70%] hover:bg-sky-400 transition" title="Apr"></div>
                <div class="w-12 bg-sky-500 rounded-t h-[95%] shadow-sm" title="Mei"></div>
                <div class="w-12 bg-sky-300 rounded-t h-[40%] hover:bg-sky-400 transition" title="Jun"></div>
            </div>
            <div class="flex justify-between px-6 pt-2 text-[11px] font-bold text-slate-400">
                <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>Mei</span><span>Jun</span>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm lg:col-span-4 flex flex-col justify-between">
            <div>
                <h4 class="text-xs font-bold text-slate-800 uppercase mb-2">Informasi Proteksi</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Sistem audit mengadopsi standar enkapsulasi data append-only. Seluruh modifikasi basis data dipantau secara ketat untuk meminimalisir fraud finansial.</p>
            </div>
            <div class="bg-slate-950 p-4 rounded-lg font-mono text-[10px] text-slate-400 shadow-inner">
                System Status: <span class="text-emerald-400 font-bold">ONLINE</span><br>
                Database Engine: InnoDB Secured
            </div>
        </div>
    </main>
</body>
</html>