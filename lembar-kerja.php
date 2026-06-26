<?php
session_start();
include 'koneksi.php';

// Cek login
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// Hanya Auditor
if ($_SESSION['role'] != 'Auditor') {
    die("Akses Ditolak.");
}

// Simpan catatan auditor
if (isset($_POST['simpan_klarifikasi'])) {

    $tx_id = intval($_POST['transaction_id']);
    $note  = mysqli_real_escape_string($conn, $_POST['catatan_auditor']);

    $update = mysqli_query($conn,"
        UPDATE transactions
        SET description = CONCAT(description,' [Temuan Audit: ','$note',']')
        WHERE transaction_id='$tx_id'
    ");

    if($update){

        $log = json_encode([
            "updated_tx"=>$tx_id,
            "note"=>$note
        ]);

        mysqli_query($conn,"
            INSERT INTO audit_trails
            (transaction_id,user_id,activity_type,new_values)
            VALUES
            ('$tx_id','2','UPDATE','$log')
        ");

        echo "<script>
        alert('Catatan Auditor berhasil disimpan.');
        window.location='lembar-kerja.php';
        </script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">

<title>Lembar Kerja Auditor</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-slate-100">

<?php include 'navbar.php'; ?>

<div class="max-w-6xl mx-auto bg-white rounded-xl shadow-lg p-8 mt-6">

<h2 class="text-2xl font-bold text-slate-800 mb-6">

⚖️ Lembar Kerja Rekonsiliasi Auditor

</h2>

<div class="overflow-x-auto">

<table class="w-full border-collapse">

<thead>

<tr class="bg-slate-200">

<th class="border p-3">ID</th>

<th class="border p-3">Deskripsi</th>

<th class="border p-3 text-center">Status</th>

<th class="border p-3 text-center">Catatan Auditor</th>

</tr>

</thead>

<tbody>

<?php

$query = mysqli_query($conn,"
SELECT
t.transaction_id,
t.description,

SUM(
CASE
WHEN d.position='Debit'
THEN d.amount
ELSE 0
END
) debit,

SUM(
CASE
WHEN d.position='Kredit'
THEN d.amount
ELSE 0
END
) kredit

FROM transactions t

JOIN transaction_details d

ON t.transaction_id=d.transaction_id

GROUP BY t.transaction_id

ORDER BY t.transaction_id DESC
");

while($row=mysqli_fetch_assoc($query)){

$status=($row['debit']==$row['kredit']);

?>

<tr class="hover:bg-slate-50">

<td class="border p-3">

TX-<?= sprintf("%03d",$row['transaction_id']); ?>

</td>

<td class="border p-3">

<?= htmlspecialchars($row['description']); ?>

</td>

<td class="border p-3 text-center">

<?php

if($status){

echo "<span class='bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold'>✓ RECONCILED</span>";

}else{

echo "<span class='bg-red-100 text-red-700 px-3 py-1 rounded-full font-bold'>✗ ERROR</span>";

}

?>

</td>

<td class="border p-3">

<form method="POST" class="flex gap-2">

<input
type="hidden"
name="transaction_id"
value="<?= $row['transaction_id']; ?>">

<input
type="text"
name="catatan_auditor"
required
placeholder="Masukkan temuan..."
class="border rounded-lg px-3 py-2 w-full">

<button
type="submit"
name="simpan_klarifikasi"
class="bg-blue-600 hover:bg-blue-700 text-white px-4 rounded-lg">

Simpan

</button>

</form>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<div class="mt-8 flex justify-between">

<a
href="dashboard-auditor.php"
class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-bold">

← Kembali ke Dashboard

</a>

<a
href="laporan.php"
class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold">

📄 Lihat Laporan

</a>

</div>

</div>

</body>
</html>