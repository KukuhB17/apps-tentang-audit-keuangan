<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "audit_keuangan";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// [Target Excel: Testing SQL Injection - Anti Bypass Malicious Input]
if (!function_exists('aman')) {
    function aman($data) {
        global $conn;
        return mysqli_real_escape_string($conn, trim(htmlspecialchars($data)));
    }
}
?>