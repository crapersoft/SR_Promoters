<?php
// Include database connection
include ('database/database.php');
session_start();

// Check if the user is logged in as admin
if (empty($_SESSION['role']) || empty($_SESSION['userName']) || $_SESSION['role'] != 'admin') {
    header('Location: ./authentication-login.php');
    die();
}

// Get the payment_id from the GET request
$full_payment_id = isset($_GET['payment_id']) ? intval($_GET['payment_id']) : 0;

// Prepare the query to fetch payment details
$query = '
    SELECT 
        fp.fp_id,
        fp.booking_id,
        fp.customer_id,
        fp.site_id,
        fp.payment_amount,
        fp.payment_date,
        fp.payment_method,
        b.Buying_Sqft,
        b.totalBuyingPrice,
        u.userName,
        u.phoneNumber,
        u.email,
        u.address,
        s.site_name,
        s.site_address
    FROM 
        fullpayments fp
    JOIN 
        booking b ON fp.booking_id = b.bookingid
    JOIN 
        user u ON fp.customer_id = u.user_id
    JOIN 
        site s ON fp.site_id = s.id
    WHERE 
        fp.fp_id = ?';
// Use prepared statements to execute the query securely
$stmt = $connection->prepare($query);
$stmt->bind_param('i', $full_payment_id);
$stmt->execute();
$result = $stmt->get_result();
$paymentData = $result->fetch_assoc();

// Check if data was found
if (!$paymentData) {
    echo 'No data found for Payment ID: ' . htmlspecialchars($full_payment_id) . '<br>';
    echo 'Executed Query: ' . htmlspecialchars($query) . '<br>';
    die('Please check if the Payment ID is correct and the database has the required data.');
}
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
    function generatePDF() {
        const { jsPDF } = window.jspdf;

        // Get payment data from the server-side PHP
        const data = <?php echo json_encode($paymentData); ?>;

        const doc = new jsPDF();

        // Function to add one invoice copy
        const addInvoice = (offsetY) => {
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
            doc.text('Payment No: ' + data.fp_id, rightX, 36 + offsetY, null, null, 'right');
            doc.text('Payment Date: ' + data.payment_date, rightX, 43 + offsetY, null, null, 'right');

            // Site Information Section
            doc.setFontSize(10);
            doc.text('Site Name: ' + data.site_name, rightX, 50 + offsetY, null, null, 'right');
            doc.setFontSize(10);
            doc.text('SqFt: ' + data.Buying_Sqft, rightX, 57 + offsetY, null, null, 'right');

            doc.text('Address: ' + data.site_address, rightX, 64 + offsetY, null, null, 'right');
            doc.text('Price: ' + data.totalBuyingPrice, rightX, 71 + offsetY, null, null, 'right');

            // Payment Details Table
            const header = ['Payee Name', 'Payment Date', 'Amount', 'Payment Mode'];
            const pdfData = [
                [data.userName, data.payment_date, data.payment_amount, data.payment_method]
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
            const totalAmount = data.payment_amount;
            doc.setFontSize(10);
            doc.text('Total Amount: ' + totalAmount, 10, doc.lastAutoTable.finalY + 8);

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
        addInvoice(160);

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