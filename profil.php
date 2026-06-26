```php
<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE user_id='$user_id'");

if (!$query) {
    die(mysqli_error($conn));
}

$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Profil Pengguna</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-slate-100">

<div class="max-w-4xl mx-auto mt-10">

<div class="bg-white rounded-xl shadow-lg overflow-hidden">

<div class="bg-slate-800 text-white p-8 text-center">

<div class="w-28 h-28 mx-auto rounded-full bg-white text-slate-800 flex items-center justify-center text-5xl">
👤
</div>

<h1 class="text-3xl font-bold mt-4">
<?= htmlspecialchars($user['full_name']); ?>
</h1>

<p class="text-slate-300 mt-2">
Username : <?= htmlspecialchars($user['username']); ?>
</p>

<?php if($user['role']=="Auditor"){ ?>

<span class="inline-block mt-4 bg-blue-500 px-4 py-2 rounded-full font-semibold">
AUDITOR
</span>

<?php } else { ?>

<span class="inline-block mt-4 bg-green-500 px-4 py-2 rounded-full font-semibold">
KLIEN
</span>

<?php } ?>

</div>


<div class="p-8">

<table class="w-full">

<tr class="border-b">
<td class="py-3 font-bold w-56">ID User</td>
<td><?= $user['user_id']; ?></td>
</tr>

<tr class="border-b">
<td class="py-3 font-bold">Nama Lengkap</td>
<td><?= htmlspecialchars($user['full_name']); ?></td>
</tr>

<tr class="border-b">
<td class="py-3 font-bold">Username</td>
<td><?= htmlspecialchars($user['username']); ?></td>
</tr>

<tr class="border-b">
<td class="py-3 font-bold">Role</td>
<td>

<?php
if($user['role']=="Auditor"){
    echo "<span class='text-blue-600 font-bold'>Auditor</span>";
}else{
    echo "<span class='text-green-600 font-bold'>Klien</span>";
}
?>

</td>
</tr>

<tr class="border-b">
<td class="py-3 font-bold">Status</td>
<td>
<span class="text-green-600 font-semibold">
Aktif
</span>
</td>
</tr>

</table>


<div class="mt-8 flex justify-between">

<?php
if($user['role']=="Auditor"){
?>

<a href="dashboard-auditor.php"
class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
← Dashboard
</a>

<?php
}else{
?>

<a href="dashboard-klien.php"
class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
← Dashboard
</a>

<?php
}
?>

<div class="space-x-2">

<a href="logout.php"
class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg">
Logout
</a>

</div>

</div>

</div>

</div>

</div>

</body>
</html>
```
