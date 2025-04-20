<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "alynnvet");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch promotions from database
$result = $mysqli->query("SELECT * FROM promotions ORDER BY created_at DESC");
$promotions = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotions & News</title>
    <link rel="stylesheet" href="promotion.css">
</head>
<body>

<div class="container">
    <h2>Latest Promotions & News</h2>
    
    <?php if (count($promotions) > 0): ?>
        <?php foreach ($promotions as $promo): ?>
            <div class="promo-card">
                <?php if (!empty($promo['image'])): ?>
                    <img src="<?php echo htmlspecialchars($promo['image']); ?>" alt="Promotion Image">
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($promo['title']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($promo['description'])); ?></p>
                <small>Posted on: <?php echo date("F j, Y", strtotime($promo['created_at'])); ?></small>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No promotions or news available at the moment.</p>
    <?php endif; ?>
</div>

</body>
</html>
