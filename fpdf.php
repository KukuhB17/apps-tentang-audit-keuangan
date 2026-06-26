<?php
session_start();
include 'koneksi.php';

// Pastikan file fpdf.php sudah ada di folder yang sama (C:\xampp\htdocs\audit_keuangan\)
require('fpdf.php'); 

// Cek login auditor atau user
if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

class PDF extends FPDF {
    // Fungsi Header (Kop Laporan)
    function Header() {
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,'LAPORAN HASIL AUDIT KEUANGAN',0,1,'C');
        $this->SetFont('Arial','I',10);
        $this->Cell(0,5,'Tanggal Cetak: ' . date('d-m-Y'),0,1,'C');
        $this->Ln(10);
        
        // Garis pembatas header
        $this->SetLineWidth(0.5);
        $this->Line(10, 25, 200, 25);
        $this->Ln(5);
    }

    // Fungsi Footer (Nomor Halaman)
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Halaman '.$this->PageNo().'/{nb}',0,0,'R');
    }
}

// Inisialisasi dokumen PDF (Ukuran A4)
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);

// ----------------------------------------------------
// 1. TABEL AUDIT TRAILS (Riwayat Aktivitas / Temuan)
// ----------------------------------------------------
$pdf->Cell(0,10,'Daftar Riwayat Audit (Audit Trails)',0,1,'L');

$pdf->SetFillColor(230, 230, 230); // Latar belakang abu-abu header
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Waktu', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'User ID', 1, 0, 'C', true);
$pdf->Cell(115, 8, 'Aktivitas / Aksi', 1, 1, 'C', true);

$pdf->SetFont('Arial','',9);

$query_trail = "SELECT * FROM audit_trails ORDER BY id DESC LIMIT 15";
$result_trail = mysqli_query($conn, $query_trail);

$no = 1;
while ($row = mysqli_fetch_assoc($result_trail)) {
    $pdf->Cell(10, 8, $no++, 1, 0, 'C');
    $pdf->Cell(35, 8, date('d-m-Y H:i', strtotime($row['created_at'])), 1, 0, 'C');
    $pdf->Cell(30, 8, $row['user_id'], 1, 0, 'C');
    $pdf->Cell(115, 8, substr($row['action'], 0, 70), 1, 1, 'L');
}

if (mysqli_num_rows($result_trail) == 0) {
    $pdf->Cell(190, 8, 'Tidak ada data riwayat audit.', 1, 1, 'C');
}

$pdf->Ln(10);

// ----------------------------------------------------
// 2. TABEL TRANSAKSI KEUANGAN
// ----------------------------------------------------
$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,10,'Ringkasan Transaksi Keuangan',0,1,'L');

$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Tanggal', 1, 0, 'C', true);
$pdf->Cell(100, 8, 'Deskripsi', 1, 0, 'C', true);
$pdf->Cell(50, 8, 'Total Amount', 1, 1, 'C', true);

$pdf->SetFont('Arial','',9);
$query_tx = "SELECT * FROM transactions ORDER BY transaction_date DESC LIMIT 15";
$result_tx = mysqli_query($conn, $query_tx);

$no_tx = 1;
while ($tx = mysqli_fetch_assoc($result_tx)) {
    $pdf->Cell(10, 8, $no_tx++, 1, 0, 'C');
    $pdf->Cell(30, 8, date('d-m-Y', strtotime($tx['transaction_date'])), 1, 0, 'C');
    $pdf->Cell(100, 8, substr($tx['description'], 0, 60), 1, 0, 'L');
    $pdf->Cell(50, 8, 'Rp ' . number_format($tx['total_amount'], 0, ',', '.'), 1, 1, 'R');
}

if (mysqli_num_rows($result_tx) == 0) {
    $pdf->Cell(190, 8, 'Tidak ada data transaksi.', 1, 1, 'C');
}

// Output file PDF langsung ke browser (Inline mode)
$pdf->Output('I', 'Laporan-Audit-Keuangan-' . date('d-m-Y') . '.pdf');
?>