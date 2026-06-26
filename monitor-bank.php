<?php
include 'middleware-auth.php';
batasiAksesKe('Auditor');
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Monitor Bank - AUDIT-APPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 min-h-screen p-6 font-sans">

<?php include 'navbar.php'; ?>

<div class="max-w-6xl mx-auto bg-white rounded-xl shadow border p-8 mt-4">

    <div class="border-b pb-4 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">
            🏦 Rekonsiliasi Rekening Koran
        </h1>

        <p class="text-sm text-slate-500">
            Monitoring transaksi rekening bank perusahaan.
        </p>
    </div>

    <?php

    $query = mysqli_query($conn,"
        SELECT *
        FROM bank_statements
        ORDER BY tanggal DESC
    ");

    ?>

    <div class="overflow-x-auto">

        <table class="w-full border-collapse">

            <thead>

                <tr class="bg-slate-100">

                    <th class="border p-3">Tanggal</th>
                    <th class="border p-3">Deskripsi</th>
                    <th class="border p-3 text-right">Nominal</th>
                    <th class="border p-3 text-center">Jenis</th>

                </tr>

            </thead>

            <tbody>

            <?php

            if(mysqli_num_rows($query)>0){

                while($row=mysqli_fetch_assoc($query)){

            ?>

                <tr class="hover:bg-slate-50">

                    <td class="border p-3">

                        <?= date('d M Y',strtotime($row['tanggal'])) ?>

                    </td>

                    <td class="border p-3">

                        <?= htmlspecialchars($row['deskripsi']) ?>

                    </td>

                    <td class="border p-3 text-right font-bold">

                        Rp <?= number_format($row['nominal'],0,',','.') ?>

                    </td>

                    <td class="border p-3 text-center">

                        <?php

                        if($row['tipe']=="Debit"){

                            echo '<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">DEBIT</span>';

                        }else{

                            echo '<span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">KREDIT</span>';

                        }

                        ?>

                    </td>

                </tr>

            <?php

                }

            }else{

            ?>

            <tr>

                <td colspan="4" class="text-center p-6 text-slate-500">

                    Belum ada data rekening bank.

                </td>

            </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>