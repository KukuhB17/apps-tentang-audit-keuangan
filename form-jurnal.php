<?php
// 1. Panggil Middleware Keamanan & Batasi Akses Hanya Untuk Klien
include 'middleware-auth.php';
batasiAksesKe('Klien');

// 2. Hubungkan ke Database
include 'koneksi.php';

// Ambil ID aktor aktif langsung dari session login yang sah
$current_user_id = $_SESSION['user_id']; 

if (isset($_POST['simpan_jurnal'])) {
    $tgl_transaksi = aman($_POST['tgl_transaksi']);
    $deskripsi     = aman($_POST['deskripsi']);
    $account_codes = $_POST['account_code'];
    $account_names = $_POST['account_name'];
    $positions     = $_POST['position'];
    $amounts       = $_POST['amount'];

    // Validasi keseimbangan di sisi backend (Server-Side Balance Enforcement)
    $total_debit = 0; 
    $total_kredit = 0;
    
    for ($i = 0; $i < count($amounts); $i++) {
        $nominal = floatval($amounts[$i]);
        if ($positions[$i] == 'Debit') { 
            $total_debit += $nominal; 
        } else { 
            $total_kredit += $nominal; 
        }
    }

    // Cek presisi balance (menghindari manipulasi inspeksi elemen di browser)
    if ($total_debit === $total_kredit && $total_debit > 0) {
        
        // Insert ke tabel utama transaksi
        $query_tx = "INSERT INTO transactions (user_id, transaction_date, description, total_amount, status) VALUES ($current_user_id, '$tgl_transaksi', '$deskripsi', $total_debit, 'Final')";
        
        if (mysqli_query($conn, $query_tx)) {
            $new_tx_id = mysqli_insert_id($conn);

            // Insert rincian baris buku ganda
            for ($i = 0; $i < count($account_codes); $i++) {
                $code  = aman($account_codes[$i]);
                $name  = aman($account_names[$i]);
                $pos   = aman($positions[$i]);
                $money = floatval($amounts[$i]);

                mysqli_query($conn, "INSERT INTO transaction_details (transaction_id, user_id, account_code, account_name, position, amount) VALUES ($new_tx_id, $current_user_id, '$code', '$name', '$pos', $money)");
            }

            // --- OTOMATISASI JEJAK AUDIT KEAMANAN (AUDIT TRAIL) ---
            $log_payload = json_encode([
                "transaction_id" => $new_tx_id, 
                "amount" => $total_debit,
                "description" => $deskripsi,
                "status" => "SUCCESS_BALANCED_ENTRY"
            ]);
            
            // Kolom disesuaikan dengan struktur tabel global: user_id, activity_type, new_values
            mysqli_query($conn, "INSERT INTO audit_trails (user_id, activity_type, new_values) VALUES ($current_user_id, 'INSERT_TRANSACTION', '$log_payload')");

            echo "<script>
                    alert('Jurnal berhasil dikunci dan dicatat ke Audit Trail!'); 
                    window.location='dashboard-klien.php';
                  </script>";
            exit;
        } else {
            echo "<script>alert('Error Basis Data: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Error Pengujian SQA: Nilai Debit & Kredit tidak balance atau bernilai 0!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUDIT-APPS - Form Entri Jurnal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen p-6 font-sans flex flex-col justify-between">

    <?php include 'navbar.php'; ?>

    <div class="flex-grow flex items-center justify-center my-6">
        <div class="max-w-4xl w-full bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 border-b pb-3 mb-4">
                📝 Form Input Entri Jurnal Baru
            </h2>
            
            <form action="" method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Tanggal Transaksi</label>
                        <input type="date" name="tgl_transaksi" required class="w-full border p-2 rounded-lg text-xs bg-slate-50 focus:outline-none focus:border-sky-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Deskripsi Jurnal</label>
                        <input type="text" name="deskripsi" placeholder="Contoh: Penerimaan Kas Operasional" required class="w-full border p-2 rounded-lg text-xs bg-slate-50 focus:outline-none focus:border-sky-500 transition">
                    </div>
                </div>

                <div class="flex justify-between items-center pt-2">
                    <span class="text-xs font-bold text-slate-700 uppercase">Rincian Baris Buku Ganda:</span>
                    <button type="button" onclick="tambahBarisAkun()" class="bg-slate-900 hover:bg-slate-800 text-white text-xs px-3 py-1.5 rounded font-semibold shadow-sm transition">+ Tambah Baris</button>
                </div>

                <div id="wrapperBaris" class="space-y-3">
                    <div class="grid grid-cols-12 gap-2 dynamic-row">
                        <input type="text" name="account_code[]" placeholder="Kode Akun" required class="col-span-3 border p-2 rounded text-xs bg-slate-50 focus:outline-none focus:border-sky-500">
                        <input type="text" name="account_name[]" placeholder="Nama Akun" required class="col-span-3 border p-2 rounded text-xs bg-slate-50 focus:outline-none focus:border-sky-500">
                        <select name="position[]" onchange="hitungOtomatis()" class="col-span-3 border p-2 rounded text-xs bg-white text-slate-700 focus:outline-none focus:border-sky-500">
                            <option value="Debit">Debit</option>
                            <option value="Kredit">Kredit</option>
                        </select>
                        <input type="number" name="amount[]" placeholder="Jumlah (Rp)" oninput="hitungOtomatis()" required class="col-span-3 border p-2 rounded text-xs bg-slate-50 input-amount focus:outline-none focus:border-sky-500">
                    </div>
                    
                    <div class="grid grid-cols-12 gap-2 dynamic-row">
                        <input type="text" name="account_code[]" placeholder="Kode Akun" required class="col-span-3 border p-2 rounded text-xs bg-slate-50 focus:outline-none focus:border-sky-500">
                        <input type="text" name="account_name[]" placeholder="Nama Akun" required class="col-span-3 border p-2 rounded text-xs bg-slate-50 focus:outline-none focus:border-sky-500">
                        <select name="position[]" onchange="hitungOtomatis()" class="col-span-3 border p-2 rounded text-xs bg-white text-slate-700 focus:outline-none focus:border-sky-500">
                            <option value="Kredit">Kredit</option>
                            <option value="Debit">Debit</option>
                        </select>
                        <input type="number" name="amount[]" placeholder="Jumlah (Rp)" oninput="hitungOtomatis()" required class="col-span-3 border p-2 rounded text-xs bg-slate-50 input-amount focus:outline-none focus:border-sky-500">
                    </div>
                </div>

                <div class="bg-slate-900 text-slate-300 p-4 rounded-xl text-xs flex justify-between items-center font-mono shadow-inner">
                    <div>Total Debit: <span id="valDebit" class="font-bold text-sky-400">Rp 0</span> | Total Kredit: <span id="valKredit" class="font-bold text-sky-400">Rp 0</span></div>
                    <div id="statusBadge" class="text-rose-400 font-bold">⚠ UNBALANCE</div>
                </div>

                <button type="submit" name="simpan_jurnal" id="btnSubmit" disabled class="w-full bg-slate-300 text-slate-500 font-bold py-3 rounded-xl text-xs cursor-not-allowed transition uppercase tracking-wider">
                    🔒 Kunci Data & Kirim ke Server
                </button>
            </form>
        </div>
    </div>

    <footer class="bg-slate-900 text-slate-400 text-[11px] py-4 text-center border-t border-slate-800 w-full rounded-t-xl mt-6">
        <p>&copy; 2026 Kelompok Aplikasi Audit Keuangan. Server-Side Balance Guard Enabled.</p>
    </footer>

    <script>
        function tambahBarisAkun() {
            const wrap = document.getElementById('wrapperBaris');
            const row = document.createElement('div');
            row.className = "grid grid-cols-12 gap-2 dynamic-row mt-2";
            row.innerHTML = `
                <input type="text" name="account_code[]" placeholder="Kode Akun" required class="col-span-3 border p-2 rounded text-xs bg-slate-50 focus:outline-none focus:border-sky-500">
                <input type="text" name="account_name[]" placeholder="Nama Akun" required class="col-span-3 border p-2 rounded text-xs bg-slate-50 focus:outline-none focus:border-sky-500">
                <select name="position[]" onchange="hitungOtomatis()" class="col-span-3 border p-2 rounded text-xs bg-white text-slate-700 focus:outline-none focus:border-sky-500">
                    <option value="Debit">Debit</option>
                    <option value="Kredit">Kredit</option>
                </select>
                <input type="number" name="amount[]" placeholder="Jumlah (Rp)" oninput="hitungOtomatis()" required class="col-span-3 border p-2 rounded text-xs bg-slate-50 input-amount focus:outline-none focus:border-sky-500">
            `;
            wrap.appendChild(row);
            hitungOtomatis();
        }

        function hitungOtomatis() {
            let d = 0, k = 0;
            document.querySelectorAll('.dynamic-row').forEach(r => {
                let pos = r.querySelector('select').value;
                let val = parseFloat(r.querySelector('.input-amount').value) || 0;
                if(pos === 'Debit') d += val; else k += val;
            });
            
            document.getElementById('valDebit').innerText = "Rp " + d.toLocaleString('id-ID');
            document.getElementById('valKredit').innerText = "Rp " + k.toLocaleString('id-ID');
            
            const btn = document.getElementById('btnSubmit');
            const bge = document.getElementById('statusBadge');
            
            if(d === k && d > 0) {
                bge.innerText = "✓ BALANCE MATCH"; 
                bge.className = "text-emerald-400 font-bold";
                btn.disabled = false; 
                btn.className = "w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 rounded-xl text-xs cursor-pointer shadow uppercase tracking-wider transition";
            } else {
                bge.innerText = "⚠ UNBALANCE"; 
                bge.className = "text-rose-400 font-bold";
                btn.disabled = true; 
                btn.className = "w-full bg-slate-300 text-slate-500 font-bold py-3 rounded-xl text-xs cursor-not-allowed uppercase tracking-wider transition";
            }
        }
    </script>
</body>
</html>