<?php
session_start();
require 'dbconnect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $contact_number = trim($_POST['contact_number']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    }
    // Validate contact number (only digits, min 10 max 15 characters)
    elseif (!preg_match('/^[0-9]{10,15}$/', $contact_number)) {
        $error = "Invalid contact number.";
    }
    // Check if passwords match
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or email already taken.";
        } else {
            // Insert user into database
            $role = 1; // Set default role sebagai customer
$insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, contact_number, role) VALUES (?, ?, ?, ?, ?)");
$insert_stmt->bind_param("ssssi", $username, $email, $hashed_password, $contact_number, $role);


            if ($insert_stmt->execute()) {
                header("Location: member_login.php?signup_success=1");
                exit();
            } else {
                $error = "Registration failed. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | PawTracker</title>
    <link rel="stylesheet" href="signup.css">
</head>
<body>

<div class="signup-container">
    <div class="left-panel">
        <img src="img/cats.png" alt="Cats Image" class="cats-img">
    </div>

    <div class="right-panel">
        <div class="signup-box">
            <img src="img/cat1.png" alt="Cat" class="cat-icon">
            <h2>CREATE ACCOUNT</h2>

            <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter username" required>

                <label>Email</label>
                <input type="email" name="email" placeholder="Enter email" required>

                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>

                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm password" required>

                <label>Contact Number</label>
                <input type="text" name="contact_number" placeholder="Enter contact number" required>

                <button type="submit">SIGN UP</button>
            </form>

            <p>Already have an account? <a href="member_login.php">Login here</a></p>
        </div>
    </div>
</div>

</body>
</html>
