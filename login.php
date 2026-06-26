<?php
include 'koneksi.php';
session_start();

$error_msg = "";

if (isset($_POST['login'])) {
    $username = isset($_POST['username']) ? aman($_POST['username']) : '';
    $password_raw = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password_raw)) {
        $error_msg = "Gagal: Username dan Password wajib diisi!";
    } else {
        $query  = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) === 1) {
            $user_data = mysqli_fetch_assoc($result);

            if (password_verify($password_raw, $user_data['password'])) {
                $_SESSION['user_id']   = $user_data['user_id'];
                $_SESSION['username']  = $user_data['username'];
                $_SESSION['full_name'] = $user_data['full_name'];
                $_SESSION['role']      = $user_data['role'];

                // Log Audit Trail Sukses Login
                $log_payload = json_encode(["username" => $username, "role" => $user_data['role'], "status" => "SUCCESS"]);
                mysqli_query($conn, "INSERT INTO audit_trails (user_id, activity_type, new_values) VALUES (".$user_data['user_id'].", 'LOGIN_USER', '$log_payload')");

                // Pengalihan Berdasarkan Wewenang Akun
                if ($_SESSION['role'] === 'Auditor') {
                    header("Location: dashboard-auditor.php");
                } else {
                    header("Location: dashboard-klien.php");
                }
                exit;
            } else {
                $error_msg = "Gagal Autentikasi: Username atau Password salah!";
            }
        } else {
            $error_msg = "Gagal Autentikasi: Username atau Password salah!";
        }
        
        // Log Audit Trail Gagal Login
        $log_failed = json_encode(["username" => $username, "status" => "FAILED"]);
        mysqli_query($conn, "INSERT INTO audit_trails (user_id, activity_type, new_values) VALUES (0, 'LOGIN_FAILED', '$log_failed')");
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>AUDIT-APPS - Login Gateway</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col justify-between font-sans p-6">

    <nav class="bg-slate-900 text-white px-6 py-4 rounded-xl flex justify-between items-center shadow mb-6 max-w-7xl mx-auto w-full">
        <div class="text-xl font-bold text-sky-400 tracking-wide">AUDIT-APPS</div>
        <div class="flex gap-6 text-sm font-medium text-slate-300">
            <a href="index.php" class="hover:text-white transition">Home</a>
            <a href="register.php" class="hover:text-white transition">Register</a>
        </div>
    </nav>

    <div class="flex items-center justify-center flex-grow mb-12">
        <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200 w-full max-w-sm">
            <div class="text-center mb-6">
                <h2 class="text-xl font-bold text-slate-900">Sign In Pengawas</h2>
                <p class="text-xs text-slate-500 mt-1">Gerbang Masuk Multi-User Terotorisasi</p>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 p-3 rounded-lg text-[11px] font-semibold">
                    ⚠ <?= $error_msg; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Username</label>
                    <input type="text" name="username" placeholder="Masukkan nama user" required class="w-full border p-2.5 rounded-lg text-xs bg-slate-50 focus:outline-none focus:border-sky-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Password</label>
                    <input type="password" name="password" placeholder="••••••••" required class="w-full border p-2.5 rounded-lg text-xs bg-slate-50 focus:outline-none focus:border-sky-500 transition">
                </div>
                <button type="submit" name="login" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-2.5 rounded-lg text-xs transition shadow-md uppercase tracking-wider">🔐 Masuk ke Sistem</button>
            </form>
            <div class="text-center mt-4">
                <p class="text-[11px] text-slate-400">Belum memiliki kredensial akun? <a href="register.php" class="text-sky-500 hover:text-sky-600 font-bold underline">Daftar Baru</a></p>
            </div>
        </div>
    </div>

    <footer class="bg-slate-900 text-slate-400 text-[11px] py-4 text-center border-t border-slate-800 w-full rounded-t-xl">
        <p>&copy; 2026 Kelompok Aplikasi Audit Keuangan. Multi-Role Authorization Guard.</p>
    </footer>
</body>
</html>