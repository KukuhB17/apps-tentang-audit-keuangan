<?php
include 'middleware-auth.php';
batasiAksesKe('Auditor');
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekonsiliasi Otomatis - AUDIT-APPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 min-h-screen p-6">

<?php include 'navbar.php'; ?>

<div class="max-w-6xl mx-auto bg-white p-8 rounded-xl shadow border mt-4">

    <h2 class="text-2xl font-bold mb-6">
        🤖 Rekonsiliasi Otomatis (Data Bank)
    </h2>

    <?php

    $query = mysqli_query($conn,"
        SELECT *
        FROM bank_statements
        ORDER BY tanggal DESC
    ");

    ?>

    <table class="w-full border-collapse">

        <thead>

            <tr class="bg-slate-100">

                <th class="border p-3">Tanggal</th>
                <th class="border p-3">Deskripsi</th>
                <th class="border p-3 text-right">Nominal</th>
                <th class="border p-3 text-center">Jenis</th>
                <th class="border p-3 text-center">Status</th>

            </tr>

        </thead>

        <tbody>

        <?php

        if(mysqli_num_rows($query)>0){

            while($row=mysqli_fetch_assoc($query)){

                if($row['tipe']=="Debit"){

                    $status="MATCH";
                    $warna="bg-green-100 text-green-700";

                }else{

                    $status="PERLU DICEK";
                    $warna="bg-yellow-100 text-yellow-700";

                }

        ?>

        <tr>

            <td class="border p-3">
                <?= date('d M Y',strtotime($row['tanggal'])) ?>
            </td>

            <td class="border p-3">
                <?= htmlspecialchars($row['deskripsi']) ?>
            </td>

            <td class="border p-3 text-right">
                Rp <?= number_format($row['nominal'],0,',','.') ?>
            </td>

            <td class="border p-3 text-center">
                <?= $row['tipe']; ?>
            </td>

            <td class="border p-3 text-center">

                <span class="px-3 py-1 rounded-full font-bold <?= $warna ?>">

                    <?= $status ?>

                </span>

            </td>

        </tr>

        <?php

            }

        }else{

        ?>

        <tr>

            <td colspan="5" class="text-center p-6">

                Belum ada data bank.

            </td>

        </tr>

        <?php } ?>

        </tbody>

    </table>

</div>

</body>
</html>