<?php
session_start();
include 'koneksi.php';

// Hentikan eksekusi jika tidak diakses melalui form (POST)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Akses ditolak. Silakan submit form melalui halaman input temuan.");
}

$tx_id = (int)($_POST['transaction_id'] ?? 0);
$temuan = trim($_POST['temuan'] ?? '');
$auditor_id = $_SESSION['user_id'] ?? 0;

if ($tx_id <= 0 || empty($temuan)) {
    die("Error: ID Transaksi tidak valid atau catatan temuan kosong.");
}

$stmt = $conn->prepare("INSERT INTO audit_findings (transaction_id, auditor_id, findings, status) VALUES (?, ?, ?, 'OPEN')");
$stmt->bind_param("iis", $tx_id, $auditor_id, $temuan);

if ($stmt->execute()) {
    echo "✅ Temuan berhasil disimpan! <a href='javascript:history.back()'>Kembali</a>";
} else {
    echo "Database Error: " . $stmt->error;
}
$stmt->close();
?>