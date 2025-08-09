<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include('database/database.php');
session_start();

// Check if the user is logged in as admin
if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    exit(); // Use exit instead of die for better practice
}

// Get the payment_id from the GET request
$payment_id = isset($_GET['payment_id']) ? intval($_GET['payment_id']) : 0;

// Prepare the query to fetch payment details
$query = "
    SELECT 
        p.*, u.*, s.*, e.*, b.*, p.total_amount, p.paid_amount,
        p.total_amount AS TotalAmount,
         p.paid_amount AS PaidAmount,
        (p.total_amount - p.paid_amount) AS balanceAmount
    FROM 
        payment p
    JOIN 
        user u ON p.customer_id = u.user_id
    JOIN 
        emis e ON p.emi_id = e.emi_ids
    JOIN 
        site s ON e.site_id = s.id
    JOIN 
        booking b ON b.emi_id = e.emi_ids
    WHERE 
        p.id = ?;";

// Use prepared statements to execute the query securely
if (!$stmt = $connection->prepare($query)) {
    die("Query preparation failed: " . $connection->error);
}
$stmt->bind_param('i', $payment_id);
$stmt->execute();
$result = $stmt->get_result();
$paymentData = $result->fetch_assoc();

// Check if data was found
if (!$paymentData) {
    die("No data found for Payment ID: " . htmlspecialchars($payment_id));
}

        // require('fpdf/fpdf.php');

        // // Ensure no output before PDF generation
        // ob_clean();
        // header('Content-Type: application/pdf');

        //     // class PDF extends FPDF {
        //     //     function Header() {
        //     //         // Set the background image
        //     //         $this->Image('Untitled.jpg', 0, 0, 210, 297); // Adjust dimensions as per the image
        //     //     }
        //     // }

        //     // Create PDF instance
        //     $pdf = new FPDF();
        //     $pdf->AddPage();
        //     $pdf->SetFont('Arial', 'B', 14);

        //     // Set transparent overlay for text clarity
        //     $pdf->SetFillColor(255, 255, 255); // White background

        //     // Set X, Y positions and create cells
        //     $pdf->SetXY(20, 30);
        //     $pdf->Cell(80, 10, 'S P Prabu', 0, 1, 'L', true);

        //     $pdf->SetXY(120, 30);
        //     $pdf->Cell(60, 10, 'Thudiyalur - Coimbatore', 0, 1, 'L', true);

        //     $pdf->SetXY(160, 50);
        //     $pdf->Cell(30, 10, '212', 0, 1, 'L', true);

        //     $pdf->SetXY(160, 60);
        //     $pdf->Cell(30, 10, '09-02-2025', 0, 1, 'L', true);

        //     $pdf->SetXY(160, 70);
        //     $pdf->Cell(30, 10, '17-Full', 0, 1, 'L', true);

        //     $pdf->SetXY(160, 80);
        //     $pdf->Cell(30, 10, '8', 0, 1, 'L', true);

        //     $pdf->SetXY(160, 90);
        //     $pdf->Cell(30, 10, '9790380526', 0, 1, 'L', true);

        //     $pdf->SetXY(100, 110);
        //     $pdf->Cell(50, 10, '10,000', 0, 1, 'C', true);

        //     $pdf->SetXY(100, 120);
        //     $pdf->Cell(50, 10, 'TEN THOUSAND RUPEES ONLY.', 0, 1, 'C', true);

        //     $pdf->SetXY(40, 140);
        //     $pdf->Cell(40, 10, '1', 0, 1, 'L', true);

        //     $pdf->SetXY(80, 140);
        //     $pdf->Cell(40, 10, 'mahesh', 0, 1, 'L', true);

        //     $pdf->SetXY(160, 150);
        //     $pdf->Cell(30, 10, 'Signature', 0, 1, 'L', true);

        //     // Save and Output PDF
        //     $pdf->Output('receipt.pdf', 'I');
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Copy Payment Invoice PDF</title>

    <!-- Include jsPDF and autoTable plugins -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.19/jspdf.plugin.autotable.min.js"></script>
</head>

<body>
    <script>
    async function generatePDF() {
        const { jsPDF } = window.jspdf;

        // Get payment data from the server-side PHP
        const data = <?php echo json_encode($paymentData); ?>;

        const doc = new jsPDF();

          const backgroundImageUrl = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSAjM7jL4YZ_uJT5Co-75IbOaR6sJdUS6jfiQ&s'; // <-- Replace with your own hosted background image

        const invoiceNumber = 'INV-' + String(data.id).padStart(5, '0');
         //onst img = await loadImage(backgroundImageUrl);
        doc.addImage(backgroundImageUrl, 'JPEG', 0, 10, 210, 140); // Full width, partial height
        // Function to add one invoice copy
        const addInvoice = (offsetY, isSecondInvoice = false) => {
            // Left Section: Company Name, Logo, and Customer Details
            doc.setFontSize(14);
            doc.text('SR Promoters', 10, 15 + offsetY);

            // Customer Information
            doc.setFontSize(12);
            doc.text('Customer', 10, 40 + offsetY);
            doc.setFontSize(10);
            doc.text('Name: ' + data.userName, 10, 50 + offsetY);
            doc.text('Phone Number: ' + data.phoneNumber, 10, 58 + offsetY);
            doc.text('Email: ' + data.email, 10, 66 + offsetY);
            doc.text('Address: ' + data.address, 10, 74 + offsetY);

            // Right Section: Invoice Details
            const rightX = 200;
            doc.setFontSize(10);
            doc.text('Payment No: ' + invoiceNumber, rightX, 36 + offsetY, null, null, 'right');
            doc.text('Payment Date: ' + data.payment_date, rightX, 43 + offsetY, null, null, 'right');

            // Site Information Section
            doc.setFontSize(10);
            doc.text('Site Name: ' + data.site_name, rightX, 50 + offsetY, null, null, 'right');
            doc.setFontSize(10);
            doc.text('SqFt: ' + data.Buying_Sqft, rightX, 57 + offsetY, null, null, 'right');

            doc.text('Address: ' + data.site_address, rightX, 64 + offsetY, null, null, 'right');
            doc.text('Price: ' + data.totalBuyingPrice, rightX, 71 + offsetY, null, null, 'right');

            // Payment Details Table
            const header = ['Payee Name', 'Payment Date', 'Amount', 'Penalty', 'Total + Penalty', 'Payment Mode'];
            const pdfData = [
                [data.userName, data.payment_date, data.amount, data.penalty, data.totalAmountAndPenalty, data.payment_mode]
            ];

            doc.autoTable({
                head: [header],
                body: pdfData,
                startY: 90 + offsetY,
                margin: {
                    top: 10,
                    left: 10,
                    right: 10
                },
                theme: 'grid',
                headStyles: {
                    fillColor: [22, 160, 133],
                    textColor: 255,
                    fontSize: 8
                },
                bodyStyles: {
                    fontSize: 8
                },
                alternateRowStyles: {
                    fillColor: [240, 240, 240]
                }
            });

            // Add Payment Summary Section
            const totalAmount = data.TotalAmount;
            doc.setFontSize(10);
            doc.text('Total Amount: ' + totalAmount, 10, doc.lastAutoTable.finalY + 8);


                // Add Pending Amount, Paid Amount, and Balance Amount for the second invoice
    if (isSecondInvoice) {
       
        const PaidAmount= data.PaidAmount;
        const balanceAmount= data.balanceAmount;
        
        doc.setFontSize(10);
        doc.text('Paid Amount: ' + PaidAmount, 10, doc.lastAutoTable.finalY + 16);
        doc.text('Balance Amount: ' + balanceAmount, 10, doc.lastAutoTable.finalY + 24);
      
    }
            // Footer Section
            doc.text('Thank you for your payment!', 105, doc.lastAutoTable.finalY + 15, null, null, 'center');
        };

        // Add first invoice copy
        addInvoice(0);

        // Draw a horizontal line between the two copies
        doc.setDrawColor(0);
        doc.setLineWidth(0.5);
        doc.line(10, 155, 200, 155);

        // Add second invoice copy (shifted down)
        addInvoice(160, true);

        // Save the PDF
        doc.save('invoice.pdf');

        // Redirect back to the previous page
        setTimeout(function() {
            window.location.href = document.referrer; // Go back to the previous page
        }, 500); // Delay to allow the PDF to finish saving
    }

    // Generate the PDF automatically on page load
    window.onload = generatePDF;
    </script>
</body>

</html>
