<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: main_menu.php");
    exit();
}

include('connection.php');
$user_id = $_SESSION['user_id'];

// Fetch overdue fines for the student
$query = "SELECT fines.id, books.title AS book_title, fines.fine_amount, fines.fine_status, 
                 fines.transaction_id, fines.fine_payment_date 
          FROM fines 
          JOIN books ON fines.book_id = books.id 
          WHERE fines.user_id = '$user_id' 
          AND fines.fine_status = 'Unpaid'";
$result = mysqli_query($conn, $query);

$receipt = "";

// Handle Payment Simulation (Stripe/PayPal Placeholder)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_fine_id'])) {
    $fine_id = $_POST['pay_fine_id'];
    $transaction_id = strtoupper(uniqid('TXN-'));
    
    // Update fine status
    $update_query = "UPDATE fines SET fine_status='Paid', fine_payment_date=NOW(), transaction_id='$transaction_id' 
                     WHERE id='$fine_id' AND user_id='$user_id'";
    
    if (mysqli_query($conn, $update_query)) {
        $receipt = "
            <div id='receipt' class='receipt-card'>
                <h2 class='receipt-title'>📜 Fine Payment Receipt</h2>
                <div class='receipt-details'>
                    <div class='receipt-row'><strong>Student ID:</strong> $user_id</div>
                    <div class='receipt-row'><strong>Transaction ID:</strong> $transaction_id</div>
                    <div class='receipt-row'><strong>Amount Paid:</strong> RM " . $_POST['fine_amount'] . "</div>
                    <div class='receipt-row'><strong>Payment Date:</strong> " . date('Y-m-d H:i:s') . "</div>
                </div>
                <div class='receipt-footer'>
                    <p>✅ Your fine has been successfully cleared.</p>
                </div>
                <div class='receipt-actions'>
                    <button onclick='printReceipt()' class='btn print-btn'>🖨 Print</button>
                    <button onclick='downloadPDF()' class='btn pdf-btn'>📥 Download PDF</button>
                </div>
            </div>
        ";
    } else {
        echo "<script>alert('Error processing payment.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check & Pay Fines - BookSys</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
     

    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
        text-align: center;
    }

    h2 {
        color: #4A148C;
        margin-top: 20px;
    }

    table {
        width: 80%;
        margin: 20px auto;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #6A1B9A;
        color: white;
        text-align: center;
    }

    tr:hover {
        background-color: #f1e4f7;
    }

    .pay-btn {
        background-color: #6A1B9A;
        color: white;
        padding: 8px 12px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        text-decoration: none;
    }

    .pay-btn:hover {
        background-color: #4A148C;
    }
</style>

</head>
<body>
    <div class="container">
        <h2>Check & Pay Fines</h2>
        <?php if (!empty($receipt)) { echo $receipt; } else { ?>
            <table border="1" cellpadding="10">
                <tr>
                    <th>Book Title</th>
                    <th>Fine Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['book_title']) ?></td>
                        <td>RM<?= number_format($row['fine_amount'], 2) ?></td>
                        <td><?= $row['fine_status'] ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="pay_fine_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="fine_amount" value="<?= $row['fine_amount'] ?>">
                                <button type="submit" class="btn pay-btn">Pay Now</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>
    <script>
        function printReceipt() {
            var receiptContent = document.getElementById("receipt").outerHTML;
            var printWindow = window.open("", "_blank");
            printWindow.document.write("<html><head><title>Print Receipt</title></head><body>" + receiptContent + "</body></html>");
            printWindow.document.close();
            printWindow.print();
        }
        function downloadPDF() {
            const { jsPDF } = window.jspdf;
            let doc = new jsPDF();
            doc.setFont("helvetica", "bold");
            doc.setFontSize(18);
            doc.text("📜 Fine Payment Receipt", 50, 20);
            let receiptDetails = document.getElementById("receipt").innerText.split("\n");
            let y = 40;
            receiptDetails.forEach(line => { doc.text(line, 20, y); y += 10; });
            doc.save("Fine_Payment_Receipt.pdf");
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
