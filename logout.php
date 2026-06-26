<?php
// 1. Mulai sesi untuk mendapatkan akses ke data sesi
session_start();

// 2. Bersihkan semua data variabel sesi (hapus array session)
$_SESSION = array();

// 3. Hancurkan cookie sesi (kunci sesi di browser)
// Ini langkah krusial untuk mencegah "Back Button Bypass"
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Hancurkan data sesi di server secara permanen
session_destroy();

// 5. Berikan feedback visual dan arahkan ke login
echo "<script>
        alert('Anda telah keluar dari sistem secara aman.'); 
        window.location.replace('login.php');
      </script>";
exit;
?>