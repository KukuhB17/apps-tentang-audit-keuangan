<?php
session_start();
include 'koneksi.php';

// Cek Login
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// Hanya Auditor
if ($_SESSION['role'] != 'Auditor') {
    die("Akses Ditolak.");
}

// ==========================
// LAPORAN NERACA
// ==========================
$debit_query = mysqli_query($conn,"
SELECT SUM(amount) AS total
FROM transaction_details
WHERE position='Debit'
");

$debit_grand = mysqli_fetch_assoc($debit_query)['total'] ?? 0;

$kredit_query = mysqli_query($conn,"
SELECT SUM(amount) AS total
FROM transaction_details
WHERE position='Kredit'
");

$kredit_grand = mysqli_fetch_assoc($kredit_query)['total'] ?? 0;

// ==========================
// LABA RUGI
// ==========================
$pendapatan_q = mysqli_query($conn,"
SELECT SUM(amount) AS total
FROM transaction_details
WHERE account_code LIKE '4%'
AND position='Kredit'
");

$total_pendapatan = mysqli_fetch_assoc($pendapatan_q)['total'] ?? 0;

$beban_q = mysqli_query($conn,"
SELECT SUM(amount) AS total
FROM transaction_details
WHERE account_code LIKE '5%'
AND position='Debit'
");

$total_beban = mysqli_fetch_assoc($beban_q)['total'] ?? 0;

$laba_rugi_bersih = $total_pendapatan - $total_beban;
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Laporan Keuangan</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>
@media print{
.no-print{
display:none;
}
}
</style>

</head>

<body class="bg-slate-100">

<?php include 'navbar.php'; ?>

<div class="max-w-4xl mx-auto mt-8 bg-white rounded-xl shadow-lg p-8">

<div class="border-b pb-4 mb-6">

<h1 class="text-3xl font-bold text-slate-800">
📄 Laporan Keuangan Final
</h1>

<p class="text-slate-500 mt-2">
Laporan otomatis berdasarkan transaksi yang telah tersimpan.
</p>

</div>

<div class="grid md:grid-cols-2 gap-6">

<div class="border rounded-xl p-5 bg-slate-50">

<h3 class="font-bold text-lg mb-4">
📊 Laporan Neraca
</h3>

<div class="flex justify-between mb-2">
<span>Total Debit</span>
<span class="font-bold">
Rp <?= number_format($debit_grand,0,',','.'); ?>
</span>
</div>

<div class="flex justify-between">
<span>Total Kredit</span>
<span class="font-bold">
Rp <?= number_format($kredit_grand,0,',','.'); ?>
</span>
</div>

</div>

<div class="border rounded-xl p-5 bg-slate-50">

<h3 class="font-bold text-lg mb-4">
💰 Laporan Laba Rugi
</h3>

<div class="flex justify-between mb-2">
<span>Total Pendapatan</span>
<span class="font-bold text-green-700">
Rp <?= number_format($total_pendapatan,0,',','.'); ?>
</span>
</div>

<div class="flex justify-between mb-2">
<span>Total Beban</span>
<span class="font-bold text-red-700">
Rp <?= number_format($total_beban,0,',','.'); ?>
</span>
</div>

<hr class="my-3">

<div class="flex justify-between">
<span class="font-bold">
Laba / Rugi Bersih
</span>

<span class="font-bold <?= ($laba_rugi_bersih>=0)?'text-green-700':'text-red-700'; ?>">
Rp <?= number_format($laba_rugi_bersih,0,',','.'); ?>
</span>

</div>

</div>

</div>

<!-- Tombol -->
<div class="flex justify-between items-center mt-8 no-print">

<a href="dashboard-auditor.php"
class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-bold">
← Kembali ke Dashboard
</a>

<div class="flex gap-3">

<a href="export-excel.php"
class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold">
📊 Export Excel
</a>

<a href="export-pdf.php"
class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-bold">
🖨 Export PDF
</a>

</div>

</div>

</div>

</body>
</html>