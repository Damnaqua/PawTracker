<?php
session_start();
include 'dbconnect.php'; // Sambung ke database

// **Check User Role**
$user_id = $_SESSION['user_id'];
$sql_role = "SELECT role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql_role);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_role = $user['role'];


// **POST ANNOUNCEMENT (Only for Admin - Role 0)**
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title']) && $_SESSION['role'] != 0) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    $image_path = null;

    // **Upload Gambar**
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create directory if not exists
        }
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        // Check if the file is an image (optional validation)
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            exit;
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit;
        }
    }

    $sql = "INSERT INTO announcements (user_id, title, content, image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $title, $content, $image_path);
    $stmt->execute();
    header("Location: announce.php");
    exit();
}


// **LIKE SYSTEM**
if (isset($_GET['like'])) {
    $announcement_id = $_GET['like'];
    $user_id = $_SESSION['user_id'];

    $check_like = $conn->prepare("SELECT * FROM announcement_likes WHERE user_id = ? AND announcement_id = ?");
    $check_like->bind_param("ii", $user_id, $announcement_id);
    $check_like->execute();
    $result = $check_like->get_result();

    if ($result->num_rows == 0) {
        $insert_like = $conn->prepare("INSERT INTO announcement_likes (user_id, announcement_id) VALUES (?, ?)");
        $insert_like->bind_param("ii", $user_id, $announcement_id);
        $insert_like->execute();
    } else {
        $delete_like = $conn->prepare("DELETE FROM announcement_likes WHERE user_id = ? AND announcement_id = ?");
        $delete_like->bind_param("ii", $user_id, $announcement_id);
        $delete_like->execute();
    }

    header("Location: announce.php");
    exit();
}

// **COMMENT SYSTEM**
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment'])) {
    $comment = $_POST['comment'];
    $announcement_id = $_POST['announcement_id'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO announcement_comments (announcement_id, user_id, comment) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $announcement_id, $user_id, $comment);
    $stmt->execute();
    header("Location: announce.php");
    exit();
}

// **Ambil semua announcement dengan username**
$sql = "SELECT a.*, u.username FROM announcements a 
        JOIN users u ON a.user_id = u.id 
        ORDER BY a.created_at DESC";
$result = $conn->query($sql);

// Function kira like
function getLikeCount($conn, $announcement_id) {
    $sql = "SELECT COUNT(*) AS like_count FROM announcement_likes WHERE announcement_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $announcement_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['like_count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <link rel="stylesheet" href="announce.css">
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: auto; background-color: #f4f4f9; }
        h1 { text-align: center; margin-top: 20px; }
        form { margin: 20px 0; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        input[type="text"], textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #45a049; }
        .announcement { background-color: #fff; border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .announcement img { max-width: 100%; height: auto; margin-top: 10px; }
        .like-btn, .comment-btn { cursor: pointer; background: none; border: none; color: blue; text-decoration: underline; }
        .like-btn:hover, .comment-btn:hover { text-decoration: none; }
        .comment-section { margin-top: 10px; padding-left: 20px; }
        .comment { margin-bottom: 5px; }
        .comment input[type="text"] { width: 80%; }
    </style>
</head>
<body>
<aside class="sidebar">
        <h2>PawTracker</h2>
        <nav>
            <ul>
                <li><a href="dashboard.php">Overview</a></li>
                <li><a href="patients.php">Patients</a></li>
                <li><a href="staff.php">Staff</a></li>
                <li><a href="status.php">Status</a></li>
                <li><a href="calendar.php">Calendar</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li class="active"><a href="announce.php">Announcement</a></li>
            </ul>
        </nav>
    </aside>

    <h1>Announcements</h1>

    <!-- Form Post Announcement -->
    <form action="announce.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Title" required><br>
        <textarea name="content" placeholder="Write your announcement..." required></textarea><br>
        <input type="file" name="image" accept="image/*"><br>
        <button type="submit">Post Announcement</button>
    </form>

    <hr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="announcement">
            <h3><?= htmlspecialchars($row['title']) ?></h3>
            <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
            <small>Posted by: <?= htmlspecialchars($row['username']) ?> | <?= $row['created_at'] ?></small>

            <!-- Paparkan gambar kalau ada -->
            <?php if (!empty($row['image'])): ?>
                <br><img src="<?= htmlspecialchars($row['image']) ?>" alt="Announcement Image">
            <?php endif; ?>

            <!-- Like Button -->
            <form action="announce.php" method="GET">
                <input type="hidden" name="like" value="<?= $row['id'] ?>">
                <button type="submit">👍 Like (<?= getLikeCount($conn, $row['id']) ?>)</button>
            </form>

            <!-- Comment Form -->
            <form action="announce.php" method="POST">
                <input type="hidden" name="announcement_id" value="<?= $row['id'] ?>">
                <input type="text" name="comment" placeholder="Add a comment..." required>
                <button type="submit">Comment</button>
            </form>

            <!-- Paparkan Komen -->
            <div class="comment-section">
                <?php
                $comment_sql = "SELECT c.*, u.username FROM announcement_comments c 
                                JOIN users u ON c.user_id = u.id 
                                WHERE c.announcement_id = ? ORDER BY c.created_at ASC";
                $stmt = $conn->prepare($comment_sql);
                $stmt->bind_param("i", $row['id']);
                $stmt->execute();
                $comments = $stmt->get_result();

                while ($comment = $comments->fetch_assoc()):
                ?>
                    <div class="comment"><b><?= htmlspecialchars($comment['username']) ?>:</b> <?= htmlspecialchars($comment['comment']) ?></div>
                <?php endwhile; ?>
            </div>
        </div>
        <hr>
    <?php endwhile; ?>

</body>
</html>
