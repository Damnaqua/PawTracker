<?php
include 'dbconnect.php';

$appointment_id = $_GET['appointment_id'] ?? null;

if ($appointment_id) {
    $query = "UPDATE appointments SET status = 'completed' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $appointment_id);
    if ($stmt->execute()) {
        echo "Appointment marked as completed.";
    } else {
        echo "Failed to update status.";
    }
}
?>
