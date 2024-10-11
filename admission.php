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
$error_message = ""; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['fullname'], $_POST['email'], $_POST['phone'], $_POST['dob'], $_POST['course'], $_POST['username'], $_POST['password'])) {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $dob = $_POST['dob'];
        $course = $_POST['course'];
        $username = $_POST['username'];
        $password = $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        

        $checkUsername = $connection->prepare("SELECT * FROM admissions WHERE username = ?");
        $checkUsername->bind_param("s", $username);
        $checkUsername->execute();
        $result = $checkUsername->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Username already exists. Please choose another one.";
        } else {
          
            $stmt = $connection->prepare("INSERT INTO admissions (fullname, email, phone, dob, course, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sssssss", $fullname, $email, $phone, $dob, $course, $username, $password);

                if ($stmt->execute()) {
                    $success = true; 
                } else {
                    $error_message = "Error executing query: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $error_message = "Error preparing statement: " . $connection->error;
            }
        }

        $checkUsername->close();
    } else {
        $error_message = "Required fields are missing.";
    }
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Status</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
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
    <h1>Admission Status</h1>
    
    <?php if ($success): ?>
        <p class="success">Application submitted successfully! You can now log in.</p>
    <?php else: ?>
        <p class="error"><?php echo !empty($error_message) ? $error_message : "Something went wrong. Please try again."; ?></p>
    <?php endif; ?>

    <a href="loginform.html">Go to Login</a>
</div>

</body>
</html>
