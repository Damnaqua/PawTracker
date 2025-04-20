<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle new comment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment'], $_POST['announcement_id'])) {
    $comment = trim($_POST['comment']);
    $announcement_id = $_POST['announcement_id'];

    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO announcement_comments (announcement_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $announcement_id, $user_id, $comment);
        $stmt->execute();
    }
    header("Location: announcement_view.php");
    exit();
}

// Handle like
if (isset($_GET['like'])) {
    $announcement_id = intval($_GET['like']);

    // Prevent multiple likes
    $check = $conn->prepare("SELECT id FROM announcement_likes WHERE user_id = ? AND announcement_id = ?");
    $check->bind_param("ii", $user_id, $announcement_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO announcement_likes (announcement_id, user_id, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $announcement_id, $user_id);
        $stmt->execute();
    }
    header("Location: announcement_view.php");
    exit();
}

// Get all announcements
$query = "SELECT a.*, u.username FROM announcements a JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC";
$announcements = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Announcements</title>
    <link rel="stylesheet" href="announcement.css">
</head>
<body>
<div class="container">
    <a href="profile_cust.php" class="back-button">← Back</a>

    <h1>Announcements</h1>
    
    <?php while ($row = $announcements->fetch_assoc()): 
        $announcement_id = $row['id'];

        // Count likes
        $like_query = $conn->query("SELECT COUNT(*) AS total FROM announcement_likes WHERE announcement_id = $announcement_id");
        $like_count = $like_query->fetch_assoc()['total'];

        // Count comments
        $comment_query = $conn->query("SELECT COUNT(*) AS total FROM announcement_comments WHERE announcement_id = $announcement_id");
        $comment_count = $comment_query->fetch_assoc()['total'];

        // Get comments
        $comments = $conn->query("SELECT c.*, u.username FROM announcement_comments c JOIN users u ON c.user_id = u.id WHERE c.announcement_id = $announcement_id ORDER BY c.created_at ASC");
    ?>
        <div class="announcement-card">
            <h2><?= htmlspecialchars($row['title']) ?></h2>
            <p class="meta">By <?= htmlspecialchars($row['username']) ?> | <?= $row['created_at'] ?></p>
            <?php if (!empty($row['image'])): ?>
                <br><img src="<?= htmlspecialchars($row['image']) ?>" alt="Announcement Image">
            <?php endif; ?>
            <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>

            <div class="actions">
                <a href="?like=<?= $row['id'] ?>" class="like-btn">👍 Like (<?= $like_count ?>)</a>
                <span class="comment-count">💬 Comments (<?= $comment_count ?>)</span>
            </div>

            <div class="comments-section">
                <?php while ($c = $comments->fetch_assoc()): ?>
                    <div class="comment">
                        <strong><?= htmlspecialchars($c['username']) ?>:</strong>
                        <p><?= htmlspecialchars($c['comment']) ?></p>
                        <small><?= $c['created_at'] ?></small>
                    </div>
                <?php endwhile; ?>

                <form method="POST" class="comment-form">
                    <input type="hidden" name="announcement_id" value="<?= $announcement_id ?>">
                    <textarea name="comment" placeholder="Write your comment..." required></textarea>
                    <button type="submit">Post Comment</button>
                </form>
            </div>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
