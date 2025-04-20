<?php
session_start();
require 'dbconnect.php'; // Ensure database connection

$error = ""; // Initialize error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // ✅ Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // ✅ Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // ✅ Use prepared statement to insert data
        $role = 0; // Set default role sebagai customer
$insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, contact_number, role) VALUES (?, ?, ?, ?, ?)");
$insert_stmt->bind_param("ssssi", $username, $email, $hashed_password, $contact_number, $role);



        if ($insert_stmt->execute()) {
            // ✅ Redirect to login after successful registration
            header("Location: login.php?registered=1");
            exit();
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | PawTracker</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>

<div class="register-container">
    <div class="left-panel">
        <img src="img/cats.png" alt="Cats Image" class="cats-img"> 
    </div>

    <div class="right-panel">
        <div class="register-box">
            <img src="img/cat1.png" alt="Cat" class="cat-icon">
            <h2>REGISTER</h2>

            <?php if (!empty($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter your username" required>

                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your email" required>

                <label>Contact Number</label>
                <input type="text" name="contact_number" placeholder="Enter your contact number" required>

                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>

                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm your password" required>

                <button type="submit">REGISTER</button>
            </form>

            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</div>

</body>
</html>
