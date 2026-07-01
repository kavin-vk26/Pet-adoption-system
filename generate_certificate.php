<?php
require 'includes/config.php';
// NOTE: FPDF library must be downloaded and placed in the 'vendor/fpdf/' directory
// If FPDF is not installed, this line will cause a fatal error.
require 'vendor/fpdf/fpdf.php'; 

$id = intval($_GET['request_id'] ?? 0);
if ($id === 0) {
    header("Location: profile.php");
    exit;
}

$request_query = $conn->query("
    SELECT ar.*, u.name AS user_name, p.name AS pet_name, p.species 
    FROM adoption_requests ar 
    JOIN users u ON u.id=ar.user_id 
    JOIN pets p ON p.id=ar.pet_id 
    WHERE ar.id=$id AND ar.status='approved'
");
$request = $request_query->fetch_assoc();

if (!$request) {
    die("Certificate not found or adoption not approved.");
}

// --- FPDF Generation Start ---
$pdf = new FPDF('L', 'mm', 'A4'); // Landscape A4
$pdf->AddPage();

// Certificate Border
$pdf->SetLineWidth(5);
$pdf->SetDrawColor(0, 123, 255); // Blue color
$pdf->Rect(10, 10, 277, 190, 'D');

// Title
$pdf->SetFont('Arial', 'B', 36);
$pdf->SetTextColor(0, 123, 255);
$pdf->Cell(0, 30, 'Official Pet Adoption Certificate', 0, 1, 'C');

// Body Text
$pdf->SetFont('Arial', '', 18);
$pdf->SetTextColor(0, 0, 0); // Black

$pdf->Ln(15);
$pdf->MultiCell(0, 10, "This is to officially certify that:", 0, 'C');

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 24);
$pdf->SetTextColor(39, 174, 96); // Green

$pdf->Cell(0, 15, htmlspecialchars(strtoupper($request['user_name'])), 0, 1, 'C');

$pdf->SetFont('Arial', '', 18);
$pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(0, 10, "has successfully adopted the wonderful pet:", 0, 'C');

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 28);
$pdf->SetTextColor(255, 99, 71); // Coral

$pdf->Cell(0, 20, htmlspecialchars($request['pet_name']) . " (" . htmlspecialchars($request['species']) . ")", 0, 1, 'C');

$pdf->SetFont('Arial', '', 16);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(10);
$pdf->Cell(0, 10, "Date of Adoption: " . date('F j, Y', strtotime($request['requested_at'])), 0, 1, 'C');

$pdf->Ln(15);
$pdf->SetFont('Arial', 'I', 14);
$pdf->Cell(0, 10, '"May your journey together be filled with joy, love, and many happy memories."', 0, 1, 'C');

$pdf->Output('I', 'Adoption_Certificate_' . $request['id'] . '.pdf');
