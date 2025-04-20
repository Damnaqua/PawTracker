<?php
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');
include 'dbconnect.php';

// Make sure user is logged in and user_id is available in session
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo "You must be logged in to view this page.";
    exit;
}

// Handle status update if POST request is received
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $appointment_id = $_POST['appointment_id'];
        $new_status = $_POST['status'];

        // Update the appointment status in the database
        $update_query = "UPDATE appointments SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $new_status, $appointment_id);

        if ($stmt->execute()) {
            // Trigger success popup after update
            echo "<script>showSuccessPopup();</script>";
        } else {
            echo "Error updating status: " . $conn->error;
        }
    }
}

// Query appointments for a given date (this could be today's date, or based on a parameter)
$date_today = date('Y-m-d');  // Use today's date or adjust to fetch appointments for another date
$query = "
SELECT a.id, p.name AS pet_name, v.vet_name AS vet_name, 
       a.appointment_date, a.appointment_time, a.status, a.appointment_type
FROM appointments a
JOIN pets p ON a.pet_id = p.id
JOIN vets v ON a.vet_id = v.vet_id
WHERE a.appointment_date = ?
ORDER BY a.appointment_time DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $date_today);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <link rel="stylesheet" href="status.css">
</head>
<body>

<aside class="sidebar">
    <h2>PawTracker</h2>
    <nav>
        <ul>
            <li><a href="dashboard.php">Overview</a></li>
            <li><a href="patients.php">Patients</a></li>
            <li><a href="staff.php">Staff</a></li>
            <li class="active"><a href="status.php">Status</a></li>
            <li><a href="calendar.php">Calendar</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="announce.php">Announcement</a></li>
        </ul>
    </nav>
</aside>

<h2>Appointments for <?php echo date('d M Y'); ?></h2>

<?php if (empty($appointments)) : ?>
    <p style="text-align:center;">No appointments scheduled for today.</p>
<?php else : ?>
    <table>
        <thead>
            <tr>
                <th>Pet Name</th>
                <th>Vet Assigned</th>
                <th>Date</th>
                <th>Time</th>
                <th>Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($appointments as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['pet_name']) ?></td>
                    <td><?= htmlspecialchars($row['vet_name']) ?></td>
                    <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                    <td><?= htmlspecialchars(substr($row['appointment_time'], 0, 5)) ?></td>
                    <td><?= htmlspecialchars(ucwords(str_replace('_', ' ', $row['appointment_type']))) ?></td>
                    <td>
                        <?php
                            // Display the status, you can change it based on the value
                            $status_label = ucfirst($row['status']);
                            echo $status_label;
                        ?>
                    </td>
                    <td>
                        <!-- Action buttons for editing status -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                            <select name="status" required>
                                <option value="scheduled" <?= ($row['status'] == 'scheduled') ? 'selected' : '' ?>>Scheduled</option>
                                <option value="ongoing" <?= ($row['status'] == 'ongoing') ? 'selected' : '' ?>>On Going</option>
                                <option value="completed" <?= ($row['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                            </select>
                            <button type="submit" name="update_status">Update Status</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php endif; ?>

<div id="successPopup">Appointment status updated successfully!</div>
<script>
// Function to show the success popup
function showSuccessPopup() {
    const successPopup = document.getElementById('successPopup');
    successPopup.classList.add('show');  // Add the 'show' class to trigger the animation

    // Hide the popup after 2 seconds
    setTimeout(() => {
        successPopup.classList.remove('show');
    }, 2000);  // The popup will disappear after 2 seconds
}

// Call this function whenever you want to display the success popup
// Example: When the status is updated successfully (You can call this after a successful AJAX request or form submission)
showSuccessPopup();
</script>

</body>
</html>
