<?php
include 'middleware-auth.php';
batasiAksesKe('Auditor');

include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Log Viewer - AUDIT-APPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 min-h-screen p-6 font-sans">

<?php include 'navbar.php'; ?>

<div class="max-w-7xl mx-auto bg-white rounded-xl shadow border p-8 mt-4">

    <div class="border-b pb-4 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">
            🛡️ Log Viewer Audit Trail
        </h1>

        <p class="text-xs text-slate-500 mt-1">
            Menampilkan seluruh aktivitas yang tercatat pada sistem.
        </p>
    </div>

<?php

$query = "
SELECT
a.log_id,
a.transaction_id,
a.user_id,
a.activity_type,
a.old_values,
a.new_values,
a.timestamp,
u.full_name

FROM audit_trails a

LEFT JOIN users u
ON a.user_id=u.user_id

ORDER BY a.timestamp DESC
";

$result=mysqli_query($conn,$query);

if(!$result){

    die("Query Error : ".mysqli_error($conn));

}

?>

<div class="overflow-x-auto">

<table class="w-full text-sm border-collapse">

<thead>

<tr class="bg-slate-100">

<th class="border p-3">Log ID</th>

<th class="border p-3">Waktu</th>

<th class="border p-3">User</th>

<th class="border p-3">Aktivitas</th>

<th class="border p-3">Transaction ID</th>

<th class="border p-3">Old Values</th>

<th class="border p-3">New Values</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($result)>0){

while($row=mysqli_fetch_assoc($result)){

?>

<tr class="hover:bg-slate-50">

<td class="border p-3 font-bold">

LOG-<?= $row['log_id']; ?>

</td>

<td class="border p-3">

<?= date('d-m-Y H:i:s',strtotime($row['timestamp'])); ?>

</td>

<td class="border p-3">

<?= htmlspecialchars($row['full_name'] ?? ('User #'.$row['user_id'])); ?>

</td>

<td class="border p-3">

<?php

if($row['activity_type']=="INSERT"){

echo "<span class='bg-green-100 text-green-700 px-2 py-1 rounded'>INSERT</span>";

}elseif($row['activity_type']=="UPDATE"){

echo "<span class='bg-yellow-100 text-yellow-700 px-2 py-1 rounded'>UPDATE</span>";

}elseif($row['activity_type']=="DELETE"){

echo "<span class='bg-red-100 text-red-700 px-2 py-1 rounded'>DELETE</span>";

}else{

echo htmlspecialchars($row['activity_type']);

}

?>

</td>

<td class="border p-3">

<?= $row['transaction_id']; ?>

</td>

<td class="border p-3">

<pre class="text-xs whitespace-pre-wrap"><?= htmlspecialchars($row['old_values']); ?></pre>

</td>

<td class="border p-3">

<pre class="text-xs whitespace-pre-wrap"><?= htmlspecialchars($row['new_values']); ?></pre>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="7" class="border p-6 text-center text-slate-500">

Belum ada data audit trail.

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<div class="mt-6">

<a href="dashboard-auditor.php"
class="bg-slate-700 hover:bg-slate-800 text-white px-5 py-2 rounded-lg">

← Kembali ke Dashboard Auditor

</a>

</div>

</div>

</body>

</html>