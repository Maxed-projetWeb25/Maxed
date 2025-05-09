<?php
session_start();

// Set a dummy user session
$_SESSION['user_id'] = 1;
$_SESSION['email'] = 'user@example.com';
$_SESSION['name'] = 'User';

// Redirect to addTicketF.php
header('Location: /projet%20web/View/Front_Office/addTicketF.php');
exit;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="/projet%20web/View/Front_Office/assets/css/bootstrap.css" rel="stylesheet">
    <link href="/projet%20web/View/Front_Office/assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background-color: #333333;
            color: #ffffff;
            padding: 20px;
        }
        .login-form {
            max-width: 400px;
            margin: 50px auto;
            background-color: #444444;
            padding: 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-form">
            <h2>Redirecting...</h2>
        </div>
    </div>

    <script src="/projet%20web/View/Front_Office/assets/js/jquery.js"></script>
    <script src="/projet%20web/View/Front_Office/assets/js/bootstrap.min.js"></script>
</body>
</html> 