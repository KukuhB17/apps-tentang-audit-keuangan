<?php
session_start();
include 'koneksi.php';

// Cek apakah sudah login
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// Hanya Auditor yang boleh mengakses
if ($_SESSION['role'] != 'Auditor') {
    die("Akses Ditolak.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Auditor - AUDIT-APPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100">

<?php include 'navbar.php'; ?>

<div class="max-w-4xl mx-auto mt-8 bg-white rounded-xl shadow-lg p-8">

    <h2 class="text-3xl font-bold text-slate-800 mb-2">
        📝 Lembar Kerja Evaluasi Auditor
    </h2>

    <p class="text-slate-500 mb-6">
        Silakan isi hasil pemeriksaan audit berikut.
    </p>

    <form action="simpan-evaluasi.php" method="POST">

        <table class="w-full border border-slate-300 mb-6">

            <thead class="bg-slate-200">

                <tr>

                    <th class="border p-3 text-left">
                        Butir Pemeriksaan
                    </th>

                    <th class="border p-3 text-center">
                        Pass
                    </th>

                    <th class="border p-3 text-center">
                        Fail
                    </th>

                </tr>

            </thead>

            <tbody>

                <tr>

                    <td class="border p-3">
                        Integritas Data (Saldo Jurnal = Buku Besar)
                    </td>

                    <td class="border text-center">
                        <input type="radio" name="integritas" value="Pass" required>
                    </td>

                    <td class="border text-center">
                        <input type="radio" name="integritas" value="Fail">
                    </td>

                </tr>

                <tr>

                    <td class="border p-3">
                        Keamanan Sistem (Audit Trail Aktif)
                    </td>

                    <td class="border text-center">
                        <input type="radio" name="keamanan" value="Pass" required>
                    </td>

                    <td class="border text-center">
                        <input type="radio" name="keamanan" value="Fail">
                    </td>

                </tr>

                <tr>

                    <td class="border p-3">
                        Validasi Jurnal (Debit = Kredit)
                    </td>

                    <td class="border text-center">
                        <input type="radio" name="validasi" value="Pass" required>
                    </td>

                    <td class="border text-center">
                        <input type="radio" name="validasi" value="Fail">
                    </td>

                </tr>

            </tbody>

        </table>

        <label class="font-semibold">
            Catatan Auditor
        </label>

        <textarea
            name="catatan"
            rows="5"
            class="w-full border rounded-lg p-3 mt-2 mb-6"
            placeholder="Masukkan catatan hasil audit..."
            required></textarea>

        <div class="flex gap-3">

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-bold">

                💾 Simpan Hasil Evaluasi

            </button>

            <a
                href="dashboard-auditor.php"
                class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-bold">

                ← Kembali ke Dashboard

            </a>

        </div>

    </form>

</div>

</body>
</html>