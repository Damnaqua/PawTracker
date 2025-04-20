<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "alynnvet");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'] ?? null;
    $vet_id = $_POST['vet_id'];
    $rating = $_POST['rating'];
    $improvements = isset($_POST['improvements']) ? implode(", ", $_POST['improvements']) : "";
    $suggestions = $mysqli->real_escape_string($_POST['suggestions']);

    $stmt = $mysqli->prepare("INSERT INTO feedback (user_id, vet_id, rating, improvement, suggestions) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $user_id, $vet_id, $rating, $improvements, $suggestions);

    if ($stmt->execute()) {
        echo "<script>alert('Feedback submitted successfully!'); window.location.href = 'profile_cust.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Get vets list
$vets = [];
$result = $mysqli->query("SELECT vet_id, vet_name FROM vets ORDER BY vet_name ASC");
while ($row = $result->fetch_assoc()) {
    $vets[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vet Feedback</title>
    <link rel="stylesheet" href="feedbackuser.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<div class="feedback-container">
    <a href="profile_cust.php" class="back-button">← Back</a>
    <h2>Feedback for Vet</h2>

    <form action="feedbackuser.php" method="POST">
        <label for="vet_id">Choose Vet:</label>
        <select name="vet_id" id="vet_id" required>
            <option value="">-- Select a Vet --</option>
            <?php foreach ($vets as $vet): ?>
                <option value="<?= $vet['vet_id'] ?>"><?= htmlspecialchars($vet['vet_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <div class="rating-section">
            <input type="radio" name="rating" value="1" id="rate1" required>
            <label for="rate1">😡</label>

            <input type="radio" name="rating" value="2" id="rate2">
            <label for="rate2">☹️</label>

            <input type="radio" name="rating" value="3" id="rate3">
            <label for="rate3">🙂</label>

            <input type="radio" name="rating" value="4" id="rate4">
            <label for="rate4">😊</label>

            <input type="radio" name="rating" value="5" id="rate5">
            <label for="rate5">😍</label>
        </div>

        <h3>What can be improved?</h3>
        <div class="improvement-options">
            <input type="checkbox" name="improvements[]" value="Overall Service" id="overall">
            <label for="overall">Overall Service</label>

            <input type="checkbox" name="improvements[]" value="Customer Support" id="support">
            <label for="support">Customer Support</label>

            <input type="checkbox" name="improvements[]" value="Pickup & Delivery" id="pickup">
            <label for="pickup">Pickup & Delivery</label>

            <input type="checkbox" name="improvements[]" value="Service & Efficiency" id="efficiency">
            <label for="efficiency">Service & Efficiency</label>

            <input type="checkbox" name="improvements[]" value="Transparency" id="transparency">
            <label for="transparency">Transparency</label>
        </div>

        <textarea name="suggestions" placeholder="Other suggestions..."></textarea>
        <button type="submit">Submit</button>
    </form>
</div>

<nav class="bottom-nav">
    <a href="homepage_cust.php"><i class="fas fa-home"></i></a>
    <a href="community.php"><i class="fas fa-users"></i></a>
    <a href="pets.php"><i class="fas fa-paw"></i></a>
    <a href="profile_cust.php" class="active"><i class="fas fa-user"></i></a>
</nav>

</body>
</html>
