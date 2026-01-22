<?php
ob_start();

include "config.php";
require('fpdf.php');
require('phpqrcode/qrlib.php');

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$uid = $_SESSION['user_id'];

/* ===== Fetch User Name & Score ===== */
$query = "
    SELECT u.name, r.score
    FROM users u
    JOIN results r ON u.id = r.user_id
    WHERE u.id = $1
    ORDER BY r.id DESC
    LIMIT 1
";
$result = pg_query_params($conn, $query, [$uid]);
$data = pg_fetch_assoc($result);

$name  = ucfirst($data['name']);
$score = $data['score'];
$date  = date("d-m-Y");
$cert_id = "CERT-$uid-" . date("Ymd");

/* ===== Generate QR Code ===== */
$qrText = "Certificate ID: $cert_id | Name: $name | Score: $score | Date: $date";
$qrFile = "qr_temp.png";
QRcode::png($qrText, $qrFile, QR_ECLEVEL_L, 4);

/* ===== Create PDF ===== */
$pdf = new FPDF('L','mm','A4');
$pdf->AddPage();

/* ===== Theme Colors ===== */
$blue = [0, 70, 140];
$gold = [200, 150, 0];

/* ===== Borders ===== */
$pdf->SetDrawColor($blue[0],$blue[1],$blue[2]);
$pdf->SetLineWidth(2);
$pdf->Rect(10,10,277,190);
$pdf->SetLineWidth(0.6);
$pdf->Rect(15,15,267,180);

/* ===== Title ===== */
$pdf->SetTextColor($blue[0],$blue[1],$blue[2]);
$pdf->SetFont('Arial','B',28);
$pdf->Ln(22);
$pdf->Cell(0,20,'CERTIFICATE OF ACHIEVEMENT',0,1,'C');

/* ===== Divider ===== */
$pdf->SetDrawColor($gold[0],$gold[1],$gold[2]);
$pdf->Line(80,60,217,60);

/* ===== Certificate Text ===== */
$pdf->Ln(12);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',16);
$pdf->Cell(0,12,'This is to certify that',0,1,'C');

$pdf->SetFont('Arial','B',22);
$pdf->SetTextColor($blue[0],$blue[1],$blue[2]);
$pdf->Cell(0,16,$name,0,1,'C');

$pdf->SetFont('Arial','',16);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(0,14,'has successfully completed the Online Examination',0,1,'C');

$pdf->Ln(4);
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,14,"Score Obtained: $score",0,1,'C');

$pdf->SetFont('Arial','',14);
$pdf->Cell(0,12,"Date of Issue: $date",0,1,'C');

/* ===== QR Code (Perfect Position – No Overlap) ===== */
$pdf->Image($qrFile, 230, 90, 35);

/* ===== Signatures ===== */
$pdf->SetY(145);
$pdf->SetFont('Arial','',12);
$pdf->Cell(90,8,'____________________',0,0,'C');
$pdf->Cell(90,8,'',0,0,'C');
$pdf->Cell(90,8,'____________________',0,1,'C');

$pdf->Cell(90,8,'Exam Coordinator',0,0,'C');
$pdf->Cell(90,8,'',0,0,'C');
$pdf->Cell(90,8,'Principal',0,1,'C');

/* ===== College Name ===== */
$pdf->Ln(6);
$pdf->SetFont('Arial','B',15);
$pdf->SetTextColor($blue[0],$blue[1],$blue[2]);
$pdf->Cell(0,10,'ANANTRAO THOPATE COLLEGE',0,1,'C');

/* ===== Certificate ID ===== */
$pdf->SetFont('Arial','I',11);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(0,8,"Certificate ID: $cert_id",0,1,'C');

/* ===== Output ===== */
$pdf->Output('D', 'Exam_Certificate.pdf');

/* ===== Cleanup ===== */
unlink($qrFile);
ob_end_flush();
?>
