<?php
// Middleware Auth (Pastikan sudah login)
include 'middleware-auth.php';

function validasiDanUpload($file_data) {
    // 1. Konfigurasi Batasan
    $max_size = 2 * 1024 * 1024; // 2 MB
    $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
    $allowed_exts = ['jpg', 'jpeg', 'png', 'pdf'];

    // 2. Validasi Ukuran
    if ($file_data['size'] > $max_size) {
        return ["status" => false, "message" => "Gagal: File terlalu besar (Maks 2MB)."];
    }

    // 3. Validasi Tipe File (MIME Type)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file_data['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        return ["status" => false, "message" => "Gagal: Tipe file tidak diizinkan."];
    }

    // 4. Validasi Ekstensi (Mencegah Bypass)
    $ext = strtolower(pathinfo($file_data['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_exts)) {
        return ["status" => false, "message" => "Gagal: Ekstensi file tidak valid."];
    }

    return ["status" => true];
}
?>