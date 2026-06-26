<?php
session_start();
include 'koneksi.php';
// Hanya Auditor yang boleh memberikan temuan
if ($_SESSION['role'] !== 'Auditor') die("Akses Ditolak.");
?>

<form action="proses-temuan.php" method="POST" class="p-6 bg-white rounded shadow">
    <h2 class="text-xl font-bold mb-4">📝 Input Temuan/Klarifikasi Auditor</h2>
    
    <label class="block mb-2">Pilih ID Transaksi untuk diklarifikasi:</label>
    <input type="number" name="transaction_id" class="w-full border p-2 mb-4" required>
    
    <label class="block mb-2">Catatan/Temuan Auditor:</label>
    <textarea name="temuan" class="w-full border p-2 mb-4" rows="4" required></textarea>
    
    <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded">Kirim Temuan</button>
</form>