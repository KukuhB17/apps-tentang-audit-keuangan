<?php
include 'cek-session.php';
include 'koneksi.php';

if ($_SESSION['role'] != "Klien") {
    echo "<script>
        alert('Halaman ini hanya untuk Klien');
        window.location='login.php';
    </script>";
    exit;
}

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE user_id='$user_id'");
$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Klien</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-slate-100">

<?php include 'navbar.php'; ?>

<div class="max-w-4xl mx-auto mt-8">

<div class="bg-white rounded-xl shadow-lg overflow-hidden">

    <div class="bg-slate-900 text-white p-8 text-center">

        <div class="w-28 h-28 rounded-full bg-white text-slate-800 mx-auto flex items-center justify-center text-5xl">
            👤
        </div>

        <h1 class="text-3xl font-bold mt-4">
            <?= htmlspecialchars($user['full_name']); ?>
        </h1>

        <p class="mt-2 text-slate-300">
            <?= htmlspecialchars($user['username']); ?>
        </p>

        <span class="inline-block mt-4 bg-green-600 px-5 py-2 rounded-full font-bold">
            KLIEN
        </span>

    </div>

    <div class="p-8">

        <h2 class="text-xl font-bold mb-6">
            Informasi Akun
        </h2>

        <table class="w-full">

            <tr class="border-b">
                <td class="py-3 font-semibold w-56">ID User</td>
                <td><?= $user['user_id']; ?></td>
            </tr>

            <tr class="border-b">
                <td class="py-3 font-semibold">Nama Lengkap</td>
                <td><?= htmlspecialchars($user['full_name']); ?></td>
            </tr>

            <tr class="border-b">
                <td class="py-3 font-semibold">Username</td>
                <td><?= htmlspecialchars($user['username']); ?></td>
            </tr>

            <tr class="border-b">
                <td class="py-3 font-semibold">Role</td>
                <td>
                    <span class="text-green-600 font-bold">
                        Klien
                    </span>
                </td>
            </tr>

            <tr class="border-b">
                <td class="py-3 font-semibold">Status</td>
                <td>
                    <span class="text-green-600 font-bold">
                        Aktif
                    </span>
                </td>
            </tr>

            <tr>
                <td class="py-3 font-semibold">Bergabung</td>
                <td><?= date('d-m-Y', strtotime($user['created_at'])); ?></td>
            </tr>

        </table>

        <div class="flex justify-between mt-8">

            <a href="dashboard-klien.php"
            class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
                ← Dashboard
            </a>

            <a href="logout.php"
            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg">
                Logout
            </a>

        </div>

    </div>

</div>

</div>

</body>
</html>