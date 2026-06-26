<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fungsi mengambil saldo berdasarkan kategori (AMAN / FIXED)
function getSaldo($kategori, $conn)
{
    $query = "SELECT SUM(t.debit - t.kredit) AS total
              FROM transactions t
              JOIN coa c ON t.account_id = c.id
              WHERE c.category = ?";

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        die("Prepare Error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $kategori);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        die("Execute Error: " . mysqli_error($conn));
    }

    $data = mysqli_fetch_assoc($result);
    return (float)($data['total'] ?? 0);
}

// Data Neraca
$aset = getSaldo('Aset', $conn);
$kewajiban = getSaldo('Kewajiban', $conn);
$ekuitas = getSaldo('Ekuitas', $conn);

$total_kewajiban_ekuitas = $kewajiban + $ekuitas;
$is_balance = abs($aset - $total_kewajiban_ekuitas) < 0.01;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Neraca</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .container { box-shadow: none; border: none; }
        }
    </style>
</head>

<body class="bg-slate-100 p-8">

<div class="container max-w-4xl mx-auto bg-white shadow-lg rounded-xl p-8 border">

    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-slate-800">LAPORAN NERACA</h1>
        <p class="text-gray-500 mt-2">
            Per Tanggal : <?= date('d-m-Y'); ?>
        </p>
    </div>

    <div class="grid grid-cols-2 gap-8">

        <!-- ASET -->
        <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
            <h2 class="text-xl font-bold text-blue-800 border-b pb-2 mb-4">ASET</h2>

            <div class="flex justify-between font-semibold text-lg">
                <span>Total Aset</span>
                <span>Rp <?= number_format($aset,0,',','.'); ?></span>
            </div>
        </div>

        <!-- KEWAJIBAN -->
        <div class="bg-green-50 p-6 rounded-lg border border-green-200">
            <h2 class="text-xl font-bold text-green-800 border-b pb-2 mb-4">
                KEWAJIBAN & EKUITAS
            </h2>

            <div class="space-y-3">

                <div class="flex justify-between">
                    <span>Kewajiban</span>
                    <span>Rp <?= number_format($kewajiban,0,',','.'); ?></span>
                </div>

                <div class="flex justify-between">
                    <span>Ekuitas</span>
                    <span>Rp <?= number_format($ekuitas,0,',','.'); ?></span>
                </div>

                <hr>

                <div class="flex justify-between font-bold text-lg">
                    <span>Total</span>
                    <span>Rp <?= number_format($total_kewajiban_ekuitas,0,',','.'); ?></span>
                </div>

            </div>
        </div>

    </div>

    <!-- STATUS -->
    <div class="mt-8">

        <?php if ($is_balance): ?>
            <div class="bg-green-100 text-green-800 border border-green-300 rounded-lg p-4 text-center font-bold text-lg">
                ✅ STATUS NERACA : BALANCE (VALID)
            </div>
        <?php else: ?>
            <div class="bg-red-100 text-red-800 border border-red-300 rounded-lg p-4 text-center font-bold text-lg">
                ❌ STATUS NERACA : TIDAK BALANCE (PERLU AUDIT)
            </div>
        <?php endif; ?>

    </div>

    <!-- Tombol -->
    <div class="mt-8 flex justify-end gap-3 no-print">

        <button onclick="history.back();"
            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
            ← Kembali
        </button>

        <button onclick="window.print();"
            class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded-lg">
            🖨 Cetak Laporan
        </button>

    </div>

</div>

</body>
</html>