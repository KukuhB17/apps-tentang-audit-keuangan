<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fungsi untuk mengambil total saldo berdasarkan tipe akun (Revenue / Expense)
function getSaldoLabaRugi($account_type, $conn) {
    // Menggunakan kolom debit dan kredit pada tabel transactions yang berelasi dengan coa
    $query = "SELECT SUM(t.debit - t.kredit) AS total_saldo 
              FROM transactions t
              JOIN coa c ON t.account_id = c.id
              WHERE c.account_type = '$account_type'";
              
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    
    // Jika nilai null kembalikan 0
    return $row['total_saldo'] ? $row['total_saldo'] : 0;
}

// Mengambil nilai Total Pendapatan dan Total Beban
$totalPendapatan = getSaldoLabaRugi('Revenue', $conn);
$totalBeban      = getSaldoLabaRugi('Expense', $conn);

// Perbaikan variabel: dari $lBersih menjadi $labaBersih agar sesuai dengan deklarasi
$labaBersih = $totalPendapatan - $totalBeban;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased py-10">

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-lg border border-gray-100">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-800">Laporan Laba Rugi</h1>
            <p class="text-sm text-gray-500 mt-1">Periode: <?= date('F Y'); ?></p>
        </div>

        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-green-800">PENDAPATAN</h2>
                    <p class="text-xs text-green-600">Total seluruh akun pendapatan</p>
                </div>
                <div class="text-xl font-bold text-green-900">
                    Rp <?= number_format(abs($totalPendapatan), 0, ',', '.'); ?>
                </div>
            </div>
        </div>

        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-red-800">BEBAN / BIAYA</h2>
                    <p class="text-xs text-red-600">Total seluruh akun beban</p>
                </div>
                <div class="text-xl font-bold text-red-900">
                    Rp <?= number_format(abs($totalBeban), 0, ',', '.'); ?>
                </div>
            </div>
        </div>

        <div class="p-6 bg-blue-50 border border-blue-200 rounded-xl text-center mb-8">
            <h3 class="text-sm font-bold text-blue-600 tracking-wider uppercase">Laba Bersih</h3>
            <div class="text-4xl font-extrabold text-blue-900 mt-2">
                Rp <?= number_format($labaBersih, 0, ',', '.'); ?>
            </div>
        </div>

        <div class="flex justify-between items-center mt-6">
            <a href="javascript:history.back()" class="bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg hover:bg-gray-300 font-semibold transition">
                Kembali
            </a>
            <a href="export-pdf.php" target="_blank" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 font-semibold transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Laporan
            </a>
        </div>

    </div>

</body>
</html>