<?php
session_start();
require 'dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ✅ Move logout logic here — immediately after session check
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

// Fetch user details
$query = "SELECT username, email, contact_number, address, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['remove_picture'])) {
        $target_file = "img/default.jpg";
    } else {
        $email = $_POST['email'];
        $contact_number = $_POST['contact_number'];
        $address = $_POST['address'];

        if (!empty($_FILES['profile_picture']['name'])) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);

            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ["jpg", "png", "gif"];
            if (in_array($file_type, $allowed_types)) {
                move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);
            } else {
                $error = "Invalid file type. Please upload a JPG, JPEG, PNG, or GIF.";
                $target_file = $user['profile_picture'];
            }
        } else {
            $target_file = $user['profile_picture'];
        }
    }

    if (isset($_POST['remove_picture'])) {
        $target_file = 'img/default.jpg'; // Gambar default
        $update_query = "UPDATE users SET profile_picture=? WHERE id=?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("si", $target_file, $user_id);
        
        if ($update_stmt->execute()) {
            header("Location: profile.php?success=1");
            exit();
        } else {
            $error = "Failed to remove picture. Try again.";
        }
    }
    

    $update_query = "UPDATE users SET email=?, contact_number=?, address=?, profile_picture=? WHERE id=?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssssi", $email, $contact_number, $address, $target_file, $user_id);

    if ($update_stmt->execute()) {
        header("Location: profile.php?success=1");
        exit();
    } else {
        $error = "Update failed. Try again.";
    }
}

// Logout functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="profile.css">
</head>

<body>
<?php if (isset($_GET['success'])): ?>
    <div id="successPopup" class="popup-success">Profile updated successfully!</div>
<?php endif; ?>

<div class="container">
    <aside class="sidebar">
        <h2>PawTracker</h2>
        <nav>
            <ul>
                <li><a href="dashboard.php">Overview</a></li>
                <li><a href="patients.php">Patients</a></li>
                <li><a href="staff.php">Staff</a></li>
                <li><a href="status.php">Status</a></li>
                <li><a href="calendar.php">Calendar</a></li>
                <li class="active"><a href="profile.php">Profile</a></li>
                <li><a href="announce.php">Announcement</a></li>
            </ul>
        </nav>
    </aside>

    <main class="profile-container">
        <h1>Profile</h1>

        <form method="POST" enctype="multipart/form-data">
            <div class="profile-details">
                <div class="profile-card">
                    <img id="profilePicPreview" src="<?php echo !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'img/default.jpg'; ?>" alt="Profile Picture">

                    <label id="uploadLabel" for="profilePicInput" class="upload-btn" style="display: none;">
                        <i class="fas fa-upload"></i> Choose Image
                    </label>
                    <input type="file" name="profile_picture" id="profilePicInput" style="display: none;">

                    <!-- Remove Profile Picture Button -->
                    <button type="submit" name="remove_picture" id="removeProfilePicBtn" class="remove-btn" style="display: none;">
                        <i class="fas fa-trash"></i> Remove Profile Picture
                    </button>

                    <div>
                        <label>Username:</label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>

                        <label>Email:</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly required required>

                        <label>Contact Number:</label>
                        <input type="text" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number']); ?>" readonly required>
                    </div>
                </div>

                <div class="address-card">
                    <label>Address:</label>
                    <textarea name="address" readonly><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
            </div>

            <!-- Extra Card -->
            <div class="extra-card">
                <h3>Additional Information</h3>
                <p>You can add any relevant details here.</p>
            </div>
            <!-- Button Row -->
            <div class="button-row">
                <div>
                    <button type="button" id="editProfileBtn" class="edit-btn">
                        <i class="fas fa-edit"></i> Edit Profile
                    </button>

                    <button type="submit" id="updateProfileBtn" class="update-btn" style="display: none;">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </div>

                <!-- Updated Logout Button -->
                <button type="submit" name="logout" class="logout-btn">Logout</button>
            </div>

        </div>
        </main>
    </div>

<script>
    document.getElementById("editProfileBtn").addEventListener("click", function() {
    let inputs = document.querySelectorAll("input:not([type='file']), textarea");
    let fileInput = document.getElementById("profilePicInput");
    let uploadLabel = document.getElementById("uploadLabel");
    let removeBtn = document.getElementById("removeProfilePicBtn");
    let updateBtn = document.getElementById("updateProfileBtn");

    let isReadOnly = inputs[0].hasAttribute("readonly");

    // Toggle readonly untuk semua input & textarea
    inputs.forEach(input => input.toggleAttribute("readonly"));

    // Toggle visibility untuk file input & buttons
    fileInput.style.display = isReadOnly ? "block" : "none";
    uploadLabel.style.display = isReadOnly ? "block" : "none";
    removeBtn.style.display = isReadOnly ? "block" : "none";
    fileInput.disabled = !isReadOnly;

    // Tukar button text antara Edit <-> Cancel
    if (isReadOnly) {
        this.innerHTML = '<i class="fas fa-times"></i> Cancel';
        updateBtn.style.display = "inline-block";
    } else {
        this.innerHTML = '<i class="fas fa-edit"></i> Edit Profile';
        updateBtn.style.display = "none";

        // Reset form jika cancel
        document.querySelector("form").reset();
        document.getElementById("profilePicPreview").src = "<?php echo htmlspecialchars($user['profile_picture']); ?>";
    }
});


    document.getElementById("profilePicInput").addEventListener("change", function(event) {
        let file = event.target.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("profilePicPreview").src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

</body>
</html>
