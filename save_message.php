<?php
session_start();
require 'dbconnect.php';

$data = json_decode(file_get_contents("php://input"), true);
$sender_id = $_SESSION['user_id'];
$message = $data['message'];

$sql = "INSERT INTO community_chat (sender_id, receiver_id, message) VALUES (?, 0, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $sender_id, $message);
$stmt->execute();
$stmt->close();
?>
