<?php
include 'middleware-auth.php';
batasiAksesKe('Auditor'); // Hanya Auditor
include 'koneksi.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$data = ['account_code'=>'', 'account_name'=>'', 'account_type'=>'Asset'];

if($id > 0) $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM coa WHERE id = $id"));

if(isset($_POST['simpan'])) {
    $code = aman($_POST['code']);
    $name = aman($_POST['name']);
    $type = aman($_POST['type']);
    
    if($id > 0) {
        mysqli_query($conn, "UPDATE coa SET account_code='$code', account_name='$name', account_type='$type' WHERE id=$id");
    } else {
        mysqli_query($conn, "INSERT INTO coa (account_code, account_name, account_type) VALUES ('$code', '$name', '$type')");
    }
    
    // Log Audit Trail
    mysqli_query($conn, "INSERT INTO audit_trails (user_id, activity_type, new_values) VALUES (".$_SESSION['user_id'].", 'MANAGE_COA', '{\"action\":\"save\", \"code\":\"$code\"}')");
    header("Location: coa.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Form COA - AUDIT-APPS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 p-6">
    <div class="max-w-md mx-auto bg-white p-6 rounded-xl border shadow">
        <form method="POST" class="space-y-4">
            <input type="text" name="code" value="<?= $data['account_code'] ?>" placeholder="Kode Akun (Contoh: 101)" required class="w-full border p-2 rounded text-xs">
            <input type="text" name="name" value="<?= $data['account_name'] ?>" placeholder="Nama Akun" required class="w-full border p-2 rounded text-xs">
            <select name="type" class="w-full border p-2 rounded text-xs">
                <?php foreach(['Asset','Liabilities','Equity','Revenue','Expense'] as $t): ?>
                    <option value="<?= $t ?>" <?= $data['account_type']==$t ? 'selected' : '' ?>><?= $t ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="simpan" class="w-full bg-slate-900 text-white py-2 rounded text-xs font-bold">SIMPAN DATA</button>
        </form>
    </div>
</body>
</html>