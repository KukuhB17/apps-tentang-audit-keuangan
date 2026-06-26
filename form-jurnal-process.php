<?php
session_start();
include 'koneksi.php';
include 'fungsi-log.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil inputan akun, debit, dan kredit
    $account_id = (int)$_POST['account_id'];
    $debit  = (float)($_POST['debit'] ?? 0);
    $kredit = (float)($_POST['kredit'] ?? 0);
    
    $user_id = $_SESSION['user_id'] ?? 0;
    $description = $_POST['description'] ?? 'Transaksi Jurnal';

    // Validasi Sederhana: Pastikan nominal tidak negatif
    if ($debit < 0 || $kredit < 0) {
        logActivity($user_id, 'INPUT_GAGAL_NILAI_NEGATIF', "Akun ID: $account_id");
        die("⚠️ Error: Nilai tidak boleh negatif.");
    }

    // Validasi Balance Sederhana (Harus seimbang dalam satu baris atau total)
    // Catatan: Anda bisa menyesuaikan logika balance form Anda di sini
    if ($debit == 0 && $kredit == 0) {
        die("⚠️ Error: Masukkan nilai Debit atau Kredit.");
    }

    // Simpan ke tabel transactions
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, account_id, description, total_amount, debit, kredit, status) VALUES (?, ?, ?, ?, ?, ?, 'Final')");
    
    // Mengambil nilai terbesar antara debit atau kredit sebagai total_amount
    $total_amount = max($debit, $kredit);
    $stmt->bind_param("iisddd", $user_id, $account_id, $description, $total_amount, $debit, $kredit);

    if ($stmt->execute()) {
        logActivity($user_id, 'INPUT_JURNAL_SUCCESS', "Akun ID: $account_id | Nominal: $total_amount");
        echo "✅ Transaksi jurnal berhasil disimpan! <a href='javascript:history.back()'>Kembali</a>";
    } else {
        echo "Database Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    die("Akses ditolak.");
}
?>