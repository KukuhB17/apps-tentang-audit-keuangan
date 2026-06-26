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

// Header agar browser download sebagai Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Keuangan_" . date("Ymd_His") . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Ambil data
$debit = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT SUM(amount) AS total
FROM transaction_details
WHERE position='Debit'
"));

$kredit = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT SUM(amount) AS total
FROM transaction_details
WHERE position='Kredit'
"));

$pendapatan = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT SUM(amount) AS total
FROM transaction_details
WHERE account_code LIKE '4%'
AND position='Kredit'
"));

$beban = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT SUM(amount) AS total
FROM transaction_details
WHERE account_code LIKE '5%'
AND position='Debit'
"));

$totalDebit = $debit['total'] ?? 0;
$totalKredit = $kredit['total'] ?? 0;
$totalPendapatan = $pendapatan['total'] ?? 0;
$totalBeban = $beban['total'] ?? 0;
$laba = $totalPendapatan - $totalBeban;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
</head>
<body>

<h2 align="center">LAPORAN KEUANGAN</h2>

<table border="1">

<tr bgcolor="#CCCCCC">
<th colspan="2">Laporan Neraca</th>
</tr>

<tr>
<td>Total Debit</td>
<td><?= number_format($totalDebit,0,',','.'); ?></td>
</tr>

<tr>
<td>Total Kredit</td>
<td><?= number_format($totalKredit,0,',','.'); ?></td>
</tr>

<tr bgcolor="#CCCCCC">
<th colspan="2">Laporan Laba Rugi</th>
</tr>

<tr>
<td>Total Pendapatan</td>
<td><?= number_format($totalPendapatan,0,',','.'); ?></td>
</tr>

<tr>
<td>Total Beban</td>
<td><?= number_format($totalBeban,0,',','.'); ?></td>
</tr>

<tr>
<td><b>Laba / Rugi Bersih</b></td>
<td><b><?= number_format($laba,0,',','.'); ?></b></td>
</tr>

</table>

<br><br>

<h3>Daftar Transaksi</h3>

<table border="1">

<tr bgcolor="#CCCCCC">
<th>No</th>
<th>Tanggal</th>
<th>Deskripsi</th>
<th>Total</th>
</tr>

<?php
$no=1;

$q=mysqli_query($conn,"
SELECT *
FROM transactions
ORDER BY transaction_date DESC
");

while($d=mysqli_fetch_assoc($q)){
?>

<tr>

<td><?= $no++; ?></td>

<td><?= date('d-m-Y',strtotime($d['transaction_date'])); ?></td>

<td><?= $d['description']; ?></td>

<td align="right">
Rp <?= number_format($d['total_amount'],0,',','.'); ?>
</td>

</tr>

<?php } ?>

</table>

</body>
</html>