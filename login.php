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
    <title>Login | PawTracker</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="login-container">
    <div class="left-panel">
        <img src="img/cats.png" alt="Cats Image" class="cats-img"> 
    </div>

    <div class="right-panel">
        <div class="login-box">
            <img src="img/cat1.png" alt="Cat" class="cat-icon">
            <h2>SIGN IN</h2>
            
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST">
                <label>Username</label>
                <input type="text" name="username" placeholder="Username" required>

                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>

                <button type="submit">LOGIN</button>
            </form>

            <a href="#">Forgot password</a>
            <p>New member? <a href="register.php">Sign up now</a></p>
        </div>
    </div>
</div>

</body>
</html>
