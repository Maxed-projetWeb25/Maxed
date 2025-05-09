<?php
session_start();

// If user is already logged in, redirect to event list
if (isset($_SESSION['user_id'])) {
    header('Location: /projet%20web/View/Front_Office/eventList.php');
    exit;
}

// Include config file for database connection
require_once 'C:/xampp/htdocs/projet web/config/config.php';

// Get the database connection
$db = Config::getInstance()->getConnection();

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        try {
            // Check if email already exists
            $query = $db->prepare("SELECT id FROM user WHERE email = ?");
            $query->execute([$email]);
            if ($query->fetch()) {
                $error = "Email already exists";
            } else {
                // Hash password and create user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query = $db->prepare("INSERT INTO user (name, email, password) VALUES (?, ?, ?)");
                $query->execute([$name, $email, $hashed_password]);

                // Get the new user's ID
                $user_id = $db->lastInsertId();

                // Set session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['email'] = $email;
                $_SESSION['name'] = $name;

                // Redirect to event list
                header('Location: /projet%20web/View/Front_Office/eventList.php');
                exit;
            }
        } catch (Exception $e) {
            $error = "An error occurred. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="/projet%20web/View/Front_Office/assets/css/bootstrap.css" rel="stylesheet">
    <link href="/projet%20web/View/Front_Office/assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background-color: #333333;
            color: #ffffff;
            padding: 20px;
        }
        .signup-form {
            max-width: 400px;
            margin: 50px auto;
            background-color: #444444;
            padding: 20px;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            background-color: #555555;
            border: 1px solid #666666;
            color: #ffffff;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="signup-form">
            <h2>Sign Up</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary">Sign Up</button>
                <a href="/projet%20web/View/Front_Office/login.php" class="btn btn-secondary">Login</a>
            </form>
        </div>
    </div>

    <script src="/projet%20web/View/Front_Office/assets/js/jquery.js"></script>
    <script src="/projet%20web/View/Front_Office/assets/js/bootstrap.min.js"></script>
</body>
</html> 