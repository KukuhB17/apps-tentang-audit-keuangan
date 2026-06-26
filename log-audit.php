<?php
// 1. Panggil Middleware Keamanan & Batasi Akses Hanya Untuk Auditor
include 'middleware-auth.php';
batasiAksesKe('Auditor');

// 2. Hubungkan ke Database
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUDIT-APPS - Live Audit Trail</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen p-6 font-sans">

    <?php include 'navbar.php'; ?>

    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm max-w-6xl mx-auto mt-4">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <div>
                <h2 class="text-sm font-bold text-slate-900 uppercase">🔍 Live Log Monitor Audit Trail (Append-Only)</h2>
                <p class="text-[10px] text-slate-400 mt-1">Data log bersifat imutabel dan dipantau secara real-time.</p>
            </div>
            <span class="text-[10px] bg-slate-900 text-sky-400 font-mono px-2 py-0.5 rounded shadow-sm">Secure Immutable View</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                        <th class="p-3">Log ID</th>
                        <th class="p-3">User Pengubah</th>
                        <th class="p-3">Aktivitas</th>
                        <th class="p-3">Waktu Kejadian</th>
                        <th class="p-3">Detail Nilai Log (JSON)</th>
                    </tr>
                </thead>
                <tbody class="text-slate-700 divide-y divide-slate-100">
                    <?php
                    // Query untuk menarik log, memastikan join dengan users untuk mendapatkan nama aktor
                    $logs = mysqli_query($conn, "SELECT a.*, u.full_name FROM audit_trails a JOIN users u ON a.user_id = u.user_id ORDER BY a.log_id DESC");
                    
                    if(mysqli_num_rows($logs) > 0) {
                        while($r = mysqli_fetch_assoc($logs)) {
                    ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 font-bold text-slate-900">LOG-00<?= $r['log_id']; ?></td>
                            <td class="p-3 font-medium text-slate-600"><?= htmlspecialchars($r['full_name']); ?></td>
                            <td class="p-3">
                                <span class="bg-emerald-100 text-emerald-800 font-extrabold px-2.5 py-0.5 rounded text-[10px] shadow-sm uppercase tracking-wider">
                                    <?= $r['activity_type']; ?>
                                </span>
                            </td>
                            <td class="p-3 font-mono text-slate-500"><?= date('d M Y, H:i:s', strtotime($r['timestamp'])); ?> WIB</td>
                            <td class="p-3">
                                <code class="bg-slate-950 text-amber-400 p-2 rounded text-[10px] block font-mono max-w-xs truncate shadow-inner" title='<?= htmlspecialchars($r['new_values']); ?>'>
                                    <?= htmlspecialchars($r['new_values']); ?>
                                </code>
                            </td>
                        </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='5' class='p-4 text-center text-slate-400 italic'>Belum ada transaksi terekam di log audit trail.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>