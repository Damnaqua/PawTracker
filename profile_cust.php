<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: member_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// **Database connection**
$mysqli = new mysqli("localhost", "root", "", "alynnvet");


// Fetch user data (profile picture & membership status)
$result = $mysqli->query("SELECT profile_picture FROM users WHERE id = '$user_id'");
$userData = $result->fetch_assoc();


$profilePic = $userData['profile_picture'] ?? 'img/haha.jpg'; 
$membershipLevel = $userData['membership_level'] ?? 'Free';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | PawTracker</title>
    <link rel="stylesheet" href="profile_cust.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

    <div class="profile-container">
        <div class="profile-header">
            <img src="<?php echo $profilePic; ?>" alt="Profile Picture" class="profile-pic">
            <span class="username"><?php echo $username; ?></span>
        </div>
        <hr>

        <div class="member-perks">
            <p>Keep on Going for More Perks!</p>
            <div class="progress-bar">
                <div class="progress" style="width: <?php echo $membershipLevel; ?>%;"></div>
            </div>
        </div>

        <div class="buttons-grid">
            <button class="grid-btn" onclick="location.href='petprofile.php'">Pet Profile</button>
            <button class="grid-btn" onclick="location.href='announcement_view.php'">Promotions & News</button>
            <button class="grid-btn" onclick="location.href='feedbackuser.php'">Feedback</button>
        </div>

        <button class="logout-btn" onclick="location.href='member_login.php'">Logout</button>
    </div>

    <nav class="bottom-nav">
        <a href="homepage_cust.php"><i class="fas fa-home"></i></a>
        <a href="community.php"><i class="fas fa-users"></i></a>
        <a href="pets.php"><i class="fas fa-paw"></i></a>
        <a href="profile_cust.php" class="active"><i class="fas fa-user"></i></a>
    </nav>

</body>
</html>
