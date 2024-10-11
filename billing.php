<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bcp_db";

$connection = new mysqli($servername, $username, $password, $dbname);

if (!isset($_SESSION['username'])) {
    header("Location: loginform.php");
    exit();
}

$stmt = $connection->prepare("SELECT amount, payment_method, status, created_at FROM payments WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$payments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            margin: 0;
            height: 100vh;
        }
    

        .sidebar {
            width: 250px;
            background-color: #1e3a8a;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        img {
            align-items: center;
        }
        .sidebar h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background-color: #1d4ed8;
        }


        .main-content {
            flex-grow: 1;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #fff;
            border-bottom: 1px solid #ccc;
        }
        .header h1 {
            font-size: 28px;
        }
        .card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 150;
            height: 150;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <img src="logoooo_dashboard.png" height="100" width="100" class="center" style="margin-bottom: 20px;">
            <h2>Dashboard</h2>
            <a href="dashboard.php">Home</a>
            <a href="payment.php">Payment</a>
            <a href="billing.php">Billing</a>
            <a href="reports.php">Reports</a>
            <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Billing History</h1>
            <a href="logout.php">Logout</a>
        </div>
        
        <div class="card">
        <table border="1">
        <tr>
            <th>Amount</th>
            <th>Payment Method</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?php echo $payment['amount']; ?></td>
                <td><?php echo $payment['payment_method']; ?></td>
                <td><?php echo $payment['status']; ?></td>
                <td><?php echo $payment['created_at']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
        </div>
    
</body>
</html>
