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

$sql = "SELECT balance FROM admissions WHERE username = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$current_balance = $row['balance'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $paymentMethod = $_POST['payment_method'];

    if ($amount <= 0 || $amount > $current_balance) {
        echo "<script>alert('Invalid payment amount. Please enter a valid amount.');</script>";
    } else {
        $paymentStatus = 'Success';

        $stmt = $connection->prepare("INSERT INTO payments (username, amount, payment_method, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdss", $_SESSION['username'], $amount, $paymentMethod, $paymentStatus);
        $stmt->execute();

        $new_balance = $current_balance - $amount;
        $update_stmt = $connection->prepare("UPDATE admissions SET balance = ? WHERE username = ?");
        $update_stmt->bind_param("ds", $new_balance, $_SESSION['username']);
        $update_stmt->execute();

        $stmt->close();
        $update_stmt->close();

        echo "<script>alert('Payment of ₱" . $amount . " was successful!'); window.location.href = 'payment.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
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
        .payment-btn {
            width: 15%;
            padding: 16px;
            background-color: #4e47e5;
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .payment-btn:hover {
            background-color: #3E38B7;
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
            <h1>Payment</h1>
            <a href="logout.php">Logout</a>
        </div>

        <div class="card">
            <h2>Current Semester</h2>
            <h3>Current Balance</h3>
            <p>₱<?php echo number_format($current_balance, 2); ?></p>
        </div>
    
        <div class="card">
            <form method="POST" action="">
                <label for="amount">Amount:</label><br>
                <input type="number" id="amount" name="amount" min="1" max="<?php echo $current_balance; ?>" required><br><br>
                
                <label for="payment_method">Payment Method:</label><br>
                <select id="payment_method" name="payment_method">
                    <option value="GCash">GCash</option>
                </select><br><br>
                
                <input type="submit" class="payment-btn" value="Make Payment">
            </form>
        </div>
    
    </div>

</body>
</html>
