<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bcp_db";

$connection = new mysqli($servername, $username, $password, $dbname);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $connection->prepare("SELECT * FROM admissions WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        echo "Username from DB: " . $row['username'] . "<br>";
        echo "Hashed password from DB: " . $hashed_password . "<br>";

        if (password_verify($password, $hashed_password)) {
            echo "Password matches! Logging in...<br>";

            $_SESSION['username'] = $username;
            $_SESSION['fullname'] = $row['fullname'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Password does not match!";
        }
    } else {
            $error_message = "No user found with that username!";
    }

    $stmt->close();
}
$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        
        .error_message {
            font-size: 18px;
            color: #333;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            margin-top: 200px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #1e3a8a;
        }
        p {
            font-size: 18px;
            color: #333;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #1e3a8a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
<img src="errormsg.png" height="100px">
    <?php if ($success): ?>
        <p class="success"></p>
    <?php else: ?>
        <p class="error"><?php echo !empty($error_message) ? $error_message : "Something went wrong. Please try again."; ?></p>
    <?php endif; ?>
    <a href="loginform.html">Go back to Login</a>

</div>

</body>
</html>
