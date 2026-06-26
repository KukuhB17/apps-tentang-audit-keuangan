```php
<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Cek role Auditor
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) != 'auditor') {
    echo "<script>
        alert('Akses ditolak! Halaman ini hanya untuk Auditor.');
        window.location='login.php';
    </script>";
    exit();
}

// Nama user
$nama = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Auditor - AUDIT APPS</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100">

<?php include 'navbar.php'; ?>

<div class="max-w-7xl mx-auto mt-8">

<div class="bg-white rounded-xl shadow-lg p-8">

<h1 class="text-3xl font-bold text-slate-800">
Dashboard Auditor
</h1>

<p class="mt-2 text-slate-600">
Selamat datang,
<b><?= htmlspecialchars($nama); ?></b>
</p>

<div class="mt-6 bg-blue-100 border-l-4 border-blue-600 text-blue-800 p-4 rounded">
🛡 Anda login sebagai <b>Auditor</b>.
</div>

<div class="grid md:grid-cols-2 gap-6 mt-8">

<div class="bg-white border rounded-xl shadow p-6">

<h2 class="text-xl font-bold mb-3">
📋 Audit Trail
</h2>

<p class="text-gray-600 mb-4">
Melihat aktivitas seluruh pengguna.
</p>

<a href="log-audit.php"
class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">
Buka Audit Trail
</a>

</div>

<div class="bg-white border rounded-xl shadow p-6">

<h2 class="text-xl font-bold mb-3">
📝 Evaluasi Audit
</h2>

<p class="text-gray-600 mb-4">
Memberikan hasil pemeriksaan dan temuan audit.
</p>

<a href="evaluasi-auditor.php"
class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700">
Buka Evaluasi
</a>

</div>

<div class="bg-white border rounded-xl shadow p-6">

<h2 class="text-xl font-bold mb-3">
📄 Laporan Keuangan
</h2>

<p class="text-gray-600 mb-4">
Melihat laporan Neraca dan Laba Rugi.
</p>

<a href="laporan.php"
class="bg-purple-600 text-white px-5 py-2 rounded hover:bg-purple-700">
Lihat Laporan
</a>

</div>

<div class="bg-white border rounded-xl shadow p-6">

<h2 class="text-xl font-bold mb-3">
👤 Profil
</h2>

<p class="text-gray-600 mb-4">
Melihat data akun Auditor.
</p>

<a href="profil.php"
class="bg-orange-600 text-white px-5 py-2 rounded hover:bg-orange-700">
Profil Saya
</a>

</div>

</div>

</div>

</div>

</body>
</html>
```
