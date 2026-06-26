<?php
include 'middleware-auth.php';
batasiAksesKe('Klien');
include 'koneksi.php';

if (isset($_POST['upload'])) {
    $tx_id = intval($_POST['transaction_id']);
    $file  = $_FILES['file_bukti'];
    
    // 1. Validasi Ukuran (2MB)
    if ($file['size'] > 2000000) {
        die("<script>alert('Error: Ukuran file melebihi 2MB!'); history.back();</script>");
    }

    // 2. Validasi Tipe File (MIME-Type Magic Bytes)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowed_mimes = ['image/jpeg', 'image/png', 'application/pdf'];
    
    if (in_array($mime, $allowed_mimes)) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_name = "TX_" . $tx_id . "_" . bin2hex(random_bytes(4)) . "." . $ext;
        $path = "uploads/" . $new_name;
        
        if (move_uploaded_file($file['tmp_name'], $path)) {
            // Simpan ke database
            mysqli_query($conn, "INSERT INTO proofs (transaction_id, file_name, file_path, uploaded_by) 
                                 VALUES ($tx_id, '".aman($file['name'])."', '$path', {$_SESSION['user_id']})");
            
            // Log Audit Trail
            $log = json_encode(["tx_id" => $tx_id, "file" => $new_name]);
            mysqli_query($conn, "INSERT INTO audit_trails (user_id, activity_type, new_values) 
                                 VALUES ({$_SESSION['user_id']}, 'UPLOAD_PROOF', '$log')");
            
            echo "<script>alert('Bukti berhasil diupload!'); window.location='dashboard-klien.php';</script>";
        }
    } else {
        echo "<script>alert('Gagal: Tipe file tidak valid!'); history.back();</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Upload Bukti - AUDIT-APPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 p-6 font-sans">
    <div class="max-w-md mx-auto bg-white p-6 rounded-xl shadow border border-slate-200">
        <h2 class="font-bold text-slate-800 mb-1 text-sm uppercase">Upload Bukti Transaksi</h2>
        <p class="text-[10px] text-slate-500 mb-4">Format: JPG, PNG, PDF (Maks 2MB)</p>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="transaction_id" value="<?= htmlspecialchars($_GET['tx_id'] ?? '') ?>">
            
            <div class="border-2 border-dashed border-slate-200 p-4 rounded-lg text-center mb-4">
                <input type="file" name="file_bukti" required class="text-[10px]">
            </div>
            
            <button type="submit" name="upload" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-2 rounded-lg text-xs font-bold transition shadow-md">
                🔒 PROSES UPLOAD DOKUMEN
            </button>
        </form>
    </div>
</body>
</html>