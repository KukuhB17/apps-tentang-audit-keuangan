<?php
ob_start();
session_start();
include 'koneksi.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

// Sesuaikan dengan lokasi fpdf.php
require_once('fpdf.php');
// Jika fpdf.php ada di folder FPDF gunakan:
// require_once('FPDF/fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,'LAPORAN HASIL AUDIT KEUANGAN',0,1,'C');

        $this->SetFont('Arial','',10);
        $this->Cell(0,6,'Tanggal Cetak : '.date('d-m-Y'),0,1,'C');

        $this->Ln(3);
        $this->Line(10,27,200,27);
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Halaman '.$this->PageNo().'/{nb}',0,0,'R');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,8,'DAFTAR RIWAYAT AUDIT',0,1);

$pdf->SetFillColor(220,220,220);

$pdf->Cell(10,8,'No',1,0,'C',true);
$pdf->Cell(35,8,'Waktu',1,0,'C',true);
$pdf->Cell(30,8,'User',1,0,'C',true);
$pdf->Cell(115,8,'Aktivitas',1,1,'C',true);

$pdf->SetFont('Arial','',9);

$query = mysqli_query($conn,"SELECT * FROM audit_trails ORDER BY id DESC LIMIT 15");

$no=1;

while($row=mysqli_fetch_assoc($query))
{
    $pdf->Cell(10,8,$no++,1,0,'C');
    $pdf->Cell(35,8,date('d-m-Y H:i',strtotime($row['created_at'])),1);
    $pdf->Cell(30,8,$row['user_id'],1);
    $pdf->Cell(115,8,substr($row['action'],0,60),1);
    $pdf->Ln();
}

$pdf->Ln(8);

$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,8,'TRANSAKSI KEUANGAN',0,1);

$pdf->SetFillColor(220,220,220);

$pdf->Cell(10,8,'No',1,0,'C',true);
$pdf->Cell(30,8,'Tanggal',1,0,'C',true);
$pdf->Cell(100,8,'Deskripsi',1,0,'C',true);
$pdf->Cell(50,8,'Total',1,1,'C',true);

$pdf->SetFont('Arial','',9);

$query = mysqli_query($conn,"SELECT * FROM transactions ORDER BY transaction_date DESC LIMIT 15");

$no=1;

while($row=mysqli_fetch_assoc($query))
{
    $pdf->Cell(10,8,$no++,1,0,'C');
    $pdf->Cell(30,8,date('d-m-Y',strtotime($row['transaction_date'])),1);
    $pdf->Cell(100,8,substr($row['description'],0,55),1);
    $pdf->Cell(50,8,'Rp '.number_format($row['total_amount'],0,',','.'),1,0,'R');
    $pdf->Ln();
}

ob_end_clean();

// D = langsung download
$pdf->Output('D','Laporan-Audit-Keuangan.pdf');
exit;