<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); 
session_start();
include 'dbconnect.php';

$owner_id = $_POST['owner_id'] ?? null;
$appointment_date = $_POST['appointment_date'] ?? null;
$appointment_time = $_POST['appointment_time'] ?? null;
$appointment_type = $_POST['appointment_type'] ?? null;
$pet_id = $_POST['pet_id'] ?? null;

if (!$owner_id || !$appointment_date || !$appointment_time || !$appointment_type || !$pet_id) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

// Check jika slot appointment sudah diambil
$checkQuery = "SELECT COUNT(*) as count FROM appointments 
               WHERE appointment_date = ? AND appointment_time = ?";
$checkStmt = $conn->prepare($checkQuery);
$checkStmt->bind_param("ss", $appointment_date, $appointment_time);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
$checkRow = $checkResult->fetch_assoc();

if ($checkRow['count'] > 0) {
    echo json_encode(["success" => false, "message" => "Slot already booked. Please choose another time."]);
    exit;
}

// Cari vet yang on duty DAN belum ada appointment "scheduled" pada tarikh tersebut
$vetQuery = "
SELECT vet_id FROM vets 
WHERE on_duty = 'Yes' 
AND vet_id NOT IN (
    SELECT vet_id FROM appointments 
    WHERE appointment_date = ? AND status = 'scheduled'
)
ORDER BY RAND() LIMIT 1";

$vetStmt = $conn->prepare($vetQuery);
$vetStmt->bind_param("s", $appointment_date);
$vetStmt->execute();
$vetResult = $vetStmt->get_result();

if (!$vetResult || $vetResult->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "No vet available at the moment."]);
    exit;
}

$vet = $vetResult->fetch_assoc();
$vet_id = $vet['vet_id'];

// Insert appointment
$query = "INSERT INTO appointments (pet_id, vet_id, appointment_date, appointment_time, appointment_type, status) 
          VALUES (?, ?, ?, ?, ?, 'scheduled')";
$stmt = $conn->prepare($query);
$stmt->bind_param("iisss", $pet_id, $vet_id, $appointment_date, $appointment_time, $appointment_type);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("iisss", $pet_id, $vet_id, $appointment_date, $appointment_time, $appointment_type);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Appointment successfully booked!"]);
} else {
    echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
