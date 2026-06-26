<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}
?>

<nav class="bg-slate-900 text-white px-6 py-4 rounded-xl shadow mb-6 max-w-7xl mx-auto">

    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">

        <!-- LEFT: LOGO + MENU -->
        <div class="flex flex-wrap items-center gap-4 text-sm font-medium">

            <!-- Logo -->
            <div class="text-xl font-bold text-sky-400 mr-4">
                <a href="<?= ($_SESSION['role']=="Auditor") ? 'dashboard-auditor.php' : 'dashboard-klien.php'; ?>">
                    AUDIT-APPS
                </a>
            </div>

            <?php if($_SESSION['role']=="Auditor"){ ?>

                <a href="dashboard-auditor.php" class="hover:text-sky-400">Dashboard</a>
                <a href="profil.php" class="hover:text-sky-400">Profil</a>
                <a href="log-audit.php" class="hover:text-sky-400">Audit Trail</a>
                <a href="evaluasi-auditor.php" class="hover:text-sky-400">Evaluasi</a>
                <a href="lembar-kerja.php" class="hover:text-sky-400">Kerja</a>
                <a href="monitor-kas.php" class="hover:text-sky-400">Kas</a>
                <a href="monitor-bank.php" class="hover:text-sky-400">Bank</a>
                <a href="buku-besar.php" class="hover:text-sky-400">Buku Besar</a>
                <a href="rekonsiliasi-otomatis.php" class="hover:text-sky-400">Rekonsiliasi</a>
                <a href="laporan.php" class="hover:text-sky-400">Laporan</a>
                <a href="laporan-neraca.php" class="hover:text-sky-400">Neraca</a>
                <a href="laporan-laba-rugi.php" class="hover:text-sky-400">Laba Rugi</a>
                <a href="log-viewer.php" class="hover:text-sky-400">Log</a>

            <?php } else { ?>

                <a href="dashboard-klien.php" class="hover:text-emerald-400">Dashboard</a>
                <a href="profil-klien.php" class="hover:text-emerald-400">Profil</a>
                <a href="form-jurnal.php" class="hover:text-emerald-400">Form Jurnal</a>
                <a href="upload-bukti.php" class="hover:text-emerald-400">Upload Bukti</a>
                <a href="laporan.php" class="hover:text-emerald-400">Laporan</a>
                <a href="bukti-transaksi.php" class="hover:text-emerald-400">Bukti Transaksi</a>

            <?php } ?>

        </div>

        <!-- RIGHT: USER -->
        <div class="flex items-center justify-between lg:justify-end gap-4">

            <span class="text-xs text-slate-300 whitespace-nowrap">
                Aktor:
                <strong class="text-white">
                    <?= htmlspecialchars($_SESSION['full_name']); ?>
                    (<?= htmlspecialchars($_SESSION['role']); ?>)
                </strong>
            </span>

            <a href="logout.php"
               class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg text-xs font-bold transition whitespace-nowrap">
                Logout
            </a>

        </div>

    </div>

</nav>