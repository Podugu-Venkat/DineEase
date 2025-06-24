<?php
require('../../adminSide/posBackend/fpdf186/fpdf.php');
require_once '../config.php';
session_start();

// Replace default reservation id with session value if GET param is missing
$reservation_id = $_GET['reservation_id'] ?? ($_SESSION['reservation_id'] ?? 1);

// Use GET parameters if provided, otherwise fallback to database query
if (isset($_GET['customer_name'])) {
    $reservationInfo = [
        'reservation_id'     => $reservation_id,  // replaced $_GET['reservation_id'] ?? 1 with $reservation_id
        'customer_name'      => $_GET['customer_name'],
        'table_id'           => $_GET['table_id'] ?? 'N/A',
        'reservation_time'   => $_GET['reservation_time'] ?? 'N/A',
        'reservation_date'   => $_GET['reservation_date'] ?? 'N/A',
        'head_count'         => $_GET['head_count'] ?? 'N/A'
        // ...special_request removed...
    ];
} else {
    function getReservationInfoById($link, $reservation_id) {
        $query = "SELECT * FROM Reservations WHERE reservation_id='$reservation_id'";
        $result = mysqli_query($link, $query);

        if ($result) {
            $reservationInfo = mysqli_fetch_assoc($result);
            // Remove special_request if exists
            unset($reservationInfo['special_request']);
            return $reservationInfo;
        } else {
            return null;
        }
    }
    $reservationInfo = getReservationInfoById($link, $reservation_id);
}

// Process preordered items passed as JSON via GET parameter "preordered"
$preorderedRaw = $_GET['preordered'] ?? '';
$preordered = $preorderedRaw ? json_decode($preorderedRaw, true) : [];
// Aggregate items by item_name
$aggregatedItems = [];
if (is_array($preordered)) {
    foreach ($preordered as $item) {
        $name = $item['item_name'];
        $quantity = (int)$item['quantity'];
        $price = (float)$item['item_price'];
        if (isset($aggregatedItems[$name])) {
            $aggregatedItems[$name]['quantity'] += $quantity;
        } else {
            $aggregatedItems[$name] = ['item_name' => $name, 'quantity' => $quantity, 'item_price' => $price];
        }
    }
}

if ($reservationInfo) {
    // Updated PDF class to insert logo and use a custom font for the receipt header.
    class PDF extends FPDF {
        function Header() {
            $pageWidth = $this->GetPageWidth();
            $logoWidth = 60; // increased logo size to 2 times
            $x = ($pageWidth - $logoWidth) / 2; // center logo horizontally
            $this->Image('logo1.png', $x, 6, $logoWidth);
            $this->Ln(30); // updated spacing after logo
            $this->SetFont('Arial', 'B', 26);
            $this->SetTextColor(0, 102, 204);
            $this->Cell(0, 15, "Reservation Receipt", 0, 1, 'C');
            $this->Ln(5);
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',12);

    // Improved table for reservation details
    $labelWidth = 50;
    $dataWidth = 130;
    $rowHeight = 10;
    
    // Build table rows for reservation details
    $fields = [
        'Reservation ID:'   => $reservationInfo['reservation_id'],
        'Customer Name:'    => $reservationInfo['customer_name'],
        'Table ID:'         => $reservationInfo['table_id'],
        'Reservation Time:' => $reservationInfo['reservation_time'],
        'Reservation Date:' => $reservationInfo['reservation_date'],
        'Head Count:'       => $reservationInfo['head_count']
    ];
    
    foreach ($fields as $label => $value) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(230,230,230);
        $pdf->Cell($labelWidth, $rowHeight, $label, 1, 0, 'L', true);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell($dataWidth, $rowHeight, $value, 1, 1, 'L', false);
    }
    
    // If there are pre-ordered items, add an improved table section
    if (count($aggregatedItems) > 0) {
        $pdf->Ln(10); // spacing
        $pdf->SetFont('Arial','B',18);  // custom font for VIP pre-ordered header
        $pdf->Cell(0,10,"Pre-Ordered Items",0,1,'C');
        
        // Table header with improved styling
        $pdf->SetFont('Arial','B',12);
        $pdf->SetFillColor(200,200,200);
        $pdf->Cell(70,10,'Item Name',1,0,'C',true);
        $pdf->Cell(30,10,'Quantity',1,0,'C',true);
        $pdf->Cell(30,10,'Price',1,0,'C',true);
        $pdf->Cell(40,10,'Subtotal',1,1,'C',true);
        
        // Table data rows
        $pdf->SetFont('Arial','',12);
        $totalPreorder = 0;
        foreach($aggregatedItems as $item) {
            $subtotal = $item['quantity'] * $item['item_price'];
            $totalPreorder += $subtotal;
            $pdf->Cell(70,10,$item['item_name'],1,0);
            $pdf->Cell(30,10,$item['quantity'],1,0,'C');
            $pdf->Cell(30,10,"Rs.{$item['item_price']}",1,0,'C');
            $pdf->Cell(40,10,"Rs.{$subtotal}",1,1,'C');
        }
        // Total row
        $pdf->SetFont('Arial','B',12);
        $pdf->SetFillColor(230,230,230);
        $pdf->Cell(130,10,'Total',1,0,'R',true);
        $pdf->Cell(40,10,"Rs.{$totalPreorder}",1,1,'C',true);
    }
    
    $pdf->Output('Reservation-Copy-ID'.$reservationInfo['reservation_id'].'.pdf', 'D');
} else {
    echo 'Invalid reservation ID or reservation not found.';
}
?>
