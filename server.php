<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
require 'vendor/autoload.php';

// Connect to MySQL
$mysqli = new mysqli("localhost", "root", "", "alynnvet");

class Chat implements MessageComponentInterface {
    protected $clients;
    private $mysqli;

    public function __construct($mysqli) {
        $this->clients = new \SplObjectStorage;
        $this->mysqli = $mysqli;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        error_log("Received: " . $msg); // ✅ Log untuk pastikan mesej diterima
    
        $data = json_decode($msg, true);
        if (!$data) {
            error_log("❌ JSON Decode Error: " . json_last_error_msg());
            return;
        }
    
        $username = $this->mysqli->real_escape_string($data['username']);
        $message = $this->mysqli->real_escape_string($data['message']);
    
        error_log("✅ Processing Message: Username: $username, Message: $message");
    
        // Ambil profile picture user
        $result = $this->mysqli->query("SELECT profile_picture FROM users WHERE username = '$username'");
        $row = $result->fetch_assoc();
        $profile_pic = $row ? $row['profile_picture'] : 'img/default.jpg';
    
        error_log("✅ Profile Pic: $profile_pic");
    
        // Insert mesej dalam database
        $query = "INSERT INTO messages (username, message, profile_picture, timestamp) 
                  VALUES ('$username', '$message', '$profile_pic', NOW())";
    
        if (!$this->mysqli->query($query)) {
            error_log("❌ MySQL Error: " . $this->mysqli->error);
        } else {
            error_log("✅ Message saved to DB: $username - $message");
        }
    
        // Hantar data kepada client
        $data['timestamp'] = date("Y-m-d H:i:s");
        $data['profile_pic'] = $profile_pic;
    
        error_log("📡 Broadcasting message to clients...");
    
        foreach ($this->clients as $client) {
            error_log("🔄 Sending to client...");
            $client->send(json_encode($data));
            error_log("✅ Message sent to client.");
        }
    }
    

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        error_log("WebSocket Error: " . $e->getMessage());
        $conn->close();
    }
}

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat($mysqli) // Pass database connection
        )
    ),
    8080
);

$server->run();
