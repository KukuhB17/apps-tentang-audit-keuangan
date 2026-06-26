<?php
include 'koneksi.php';

$error_msg = "";
$success_msg = "";

if (isset($_POST['register'])) {
    // 1. Bersihkan input (Anti-XSS & SQL Injection)
    $username  = isset($_POST['username']) ? aman($_POST['username']) : '';
    $password_raw = isset($_POST['password']) ? $_POST['password'] : '';
    $full_name = isset($_POST['full_name']) ? aman($_POST['full_name']) : '';
    $role      = isset($_POST['role']) ? aman($_POST['role']) : '';

    // --- SERVER-SIDE VALIDATION ---
    if (empty($username) || empty($password_raw) || empty($full_name) || empty($role)) {
        $error_msg = "Gagal: Semua kolom formulir wajib diisi!";
    } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
        $error_msg = "Format Username Salah: Gunakan huruf dan angka tanpa spasi!";
    } elseif (strlen($username) < 4 || strlen($username) > 20) {
        $error_msg = "Batas Karakter: Username harus di antara 4 sampai 20 karakter!";
    } elseif (strlen($password_raw) < 6) {
        $error_msg = "Keamanan Password: Password terlalu pendek, minimal harus 6 karakter!";
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $full_name)) {
        $error_msg = "Format Nama Salah: Nama lengkap hanya boleh berisi huruf dan spasi!";
    } elseif ($role !== 'Klien' && $role !== 'Auditor') {
        $error_msg = "Manipulasi Data: Pilihan Role tidak valid!";
    } else {
        // Cek Keunikan Username agar tidak duplikat
        $cek_user = mysqli_query($conn, "SELECT username FROM users WHERE username = '$username'");
        if (mysqli_num_rows($cek_user) > 0) {
            $error_msg = "Registrasi Gagal: Username '$username' sudah terdaftar.";
        } else {
            // Enkripsi password aman dengan BCRYPT
            $password_secure = password_hash($password_raw, PASSWORD_BCRYPT);

            // Insert User Baru
            $query = "INSERT INTO users (username, password, full_name, role) VALUES ('$username', '$password_secure', '$full_name', '$role')";
            if (mysqli_query($conn, $query)) {
                $new_user_id = mysqli_insert_id($conn);

                // Catat Log Pendaftaran Akun Baru ke Audit Trail
                $log_payload = json_encode(["registered_user" => $username, "role" => $role, "validation" => "PASSED"]);
                mysqli_query($conn, "INSERT INTO audit_trails (user_id, activity_type, new_values) VALUES ($new_user_id, 'INSERT_USER', '$log_payload')");

                echo "<script>alert('Registrasi akun berhasil disave! Silakan lakukan login.'); window.location='login.php';</script>";
                exit;
            } else {
                $error_msg = "Error Basis Data: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUDIT-APPS - Validasi Form Registrasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen p-6 font-sans flex flex-col justify-between">

    <nav class="bg-slate-900 text-white px-6 py-4 rounded-xl flex justify-between items-center shadow mb-6 max-w-7xl mx-auto w-full">
        <div class="text-xl font-bold text-sky-400 tracking-wide">AUDIT-APPS</div>
        <div class="flex gap-6 text-sm font-medium text-slate-300">
            <a href="index.php" class="hover:text-white transition">Home</a>
            <a href="login.php" class="hover:text-white transition">Login</a>
            <a href="register.php" class="text-white border-b-2 border-sky-400 pb-1">Register</a>
        </div>
        <div class="text-xs text-slate-400">Status: <span class="text-white font-bold">Validasi Registrasi</span></div>
    </nav>

    <div class="flex items-center justify-center flex-grow mb-12">
        <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200 w-full max-w-sm">
            <div class="text-center mb-6">
                <h2 class="text-xl font-bold text-slate-900">Registrasi Akun Baru</h2>
                <p class="text-xs text-slate-500 mt-1">Sistem Pengawasan Tervalidasi & Proteksi Input</p>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 p-3 rounded-lg text-[11px] font-semibold">
                    ⚠ <?= $error_msg; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" name="full_name" placeholder="Contoh: Kukuh Dwi" required 
                           pattern="[a-zA-Z\s]+" title="Nama lengkap hanya boleh berisi huruf dan spasi."
                           value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                           class="w-full border p-2.5 rounded-lg text-xs bg-slate-50 focus:outline-none focus:border-sky-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Username</label>
                    <input type="text" name="username" placeholder="Huruf & angka tanpa spasi" required 
                           minlength="4" maxlength="20" pattern="[a-zA-Z0-9]+" title="Username hanya boleh berisi huruf dan angka tanpa spasi."
                           value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           class="w-full border p-2.5 rounded-lg text-xs bg-slate-50 focus:outline-none focus:border-sky-500 transition">
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Password</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter" required 
                           minlength="6" class="w-full border p-2.5 rounded-lg text-xs bg-slate-50 focus:outline-none focus:border-sky-500 transition">
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Hak Akses (Role)</label>
                    <select name="role" required class="w-full border p-2.5 rounded-lg text-xs bg-white text-slate-700 focus:outline-none focus:border-sky-500">
                        <option value="Klien" <?= (isset($_POST['role']) && $_POST['role'] == 'Klien') ? 'selected' : ''; ?>>Klien (Entri Jurnal)</option>
                        <option value="Auditor" <?= (isset($_POST['role']) && $_POST['role'] == 'Auditor') ? 'selected' : ''; ?>>Auditor (Pengawasan)</option>
                    </select>
                </div>
                
                <button type="submit" name="register" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-2.5 rounded-lg text-xs transition shadow-md uppercase tracking-wider">
                    Kunci & Daftarkan Akun
                </button>
            </form>
            
            <div class="text-center mt-4">
                <p class="text-[11px] text-slate-500">Sudah memiliki kredensial akun? 
                    <a href="login.php" class="text-sky-500 hover:text-sky-600 font-bold underline transition">Silakan Login</a>
                </p>
            </div>
        </div>
    </div>

    <footer class="bg-slate-900 text-slate-400 text-[11px] py-4 text-center border-t border-slate-800 w-full rounded-t-xl">
        <p>&copy; 2026 Kelompok Aplikasi Audit Keuangan. Two-Tier Validation Guard Active.</p>
    </footer>

</body>
</html>