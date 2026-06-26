<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Middleware untuk mengamankan halaman finansial dari user yang belum login
 */
function cekAutentikasi() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        echo "<script>
                alert('Akses Ditolak! Anda harus login terlebih dahulu untuk mengakses data finansial.'); 
                window.location='login.php';
              </script>";
        exit;
    }
}

/**
 * Middleware untuk memastikan hanya Role tertentu yang bisa membuka halaman
 * @param string $role_wajib (Contoh: 'Auditor' atau 'Klien')
 */
function batasiAksesKe($role_wajib) {
    // 1. Pastikan sudah login dulu
    cekAutentikasi();
    
    // 2. Cek apakah role sesuai dengan wewenang halaman
    if ($_SESSION['role'] !== $role_wajib) {
        // Catat percobaan akses ilegal ke Audit Trail demi keamanan SQA
        include 'koneksi.php';
        $user_id = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        $halaman_diakses = basename($_SERVER['PHP_SELF']);
        
        $log_payload = json_encode([
            "username" => $username,
            "attempted_page" => $halaman_diakses,
            "status" => "UNAUTHORIZED_BYPASS_ATTEMPT"
        ]);
        
        mysqli_query($conn, "INSERT INTO audit_trails (user_id, activity_type, new_values) VALUES ($user_id, 'ILLEGAL_ACCESS_ATTEMPT', '$log_payload')");

        echo "<script>
                alert('Peringatan Keamanan: Anda tidak memiliki hak akses (Privilese) untuk halaman finansial ini!'); 
                window.location = '" . ($_SESSION['role'] === 'Auditor' ? 'dashboard-auditor.php' : 'dashboard-klien.php') . "';
              </script>";
        exit;
    }
}
?>