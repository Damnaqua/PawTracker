<?php
session_start();
require 'dbconnect.php'; // Database connection

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statements
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) { 
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

        if ($user['role'] == 0) {
                header("Location: dashboard.php"); // Admin Dashboard
            } else {
                header("Location: homepage_cust.php"); // Customer Homepage
            }
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with this email.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Login | PawTracker</title>
    <link rel="stylesheet" href="member_login.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <img src="img/logo.png" alt="Logo" class="logo">
        <h2>PawTracker | Login</h2>
        
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter your username" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>

            <button type="submit">LOGIN</button>
        </form>

        <p>New member? <a href="signup.php">Sign up now</a></p>
    </div>
</div>

</body>
</html>
