<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Cek apakah session role dan user_id sudah ada
if (!isset($_SESSION['role']) || !isset($_SESSION['user_id'])) {
    // Jika tidak ada session (belum login), paksa keluar ke login.php
    echo "<script>
            alert('Akses Ditolak! Anda harus login terlebih dahulu untuk mengakses halaman ini.'); 
            window.location='login.php';
          </script>";
    exit;
}

// 2. Proteksi Tambahan: Mencegah Session Fixation (Memperbarui ID Session agar tidak dibajak)
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} else if (time() - $_SESSION['CREATED'] > 1800) { // Berlaku selama 30 menit
    session_regenerate_id(true);    // Perbarui ID session lama dengan yang baru
    $_SESSION['CREATED'] = time();  // Reset waktu buat baru
}
?>