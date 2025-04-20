<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: member_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// **Connect database untuk load chat history dengan JOIN users**
$mysqli = new mysqli("localhost", "root", "", "alynnvet");

if ($mysqli->connect_error) {
    die("Database Connection Failed: " . $mysqli->connect_error);
}

$query = "SELECT username, message, COALESCE(profile_picture, 'img/default.jpg') AS profile_pic, timestamp FROM messages ORDER BY timestamp ASC";
$result = $mysqli->query($query);

if (!$result) {
    die("❌ Query Failed: " . $mysqli->error); // Debug kalau query gagal
}

$chatHistory = [];
while ($row = $result->fetch_assoc()) {
    $chatHistory[] = $row;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Chat | PawTracker</title>
    <link rel="stylesheet" href="community.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        let ws;
        let messageLog = new Set();
        
        function connectWebSocket() {
            ws = new WebSocket("ws://localhost:8080");
            ws.onopen = () => console.log("Connected to WebSocket Server");
            
            ws.onmessage = function (event) {
    console.log("Raw message received:", event.data); // Debug incoming data

    let data = JSON.parse(event.data);
    let msgKey = data.username + ":" + data.message + ":" + data.timestamp;

    if (!messageLog.has(msgKey)) {
        messageLog.add(msgKey);
        let profilePic = data.profile_pic || "img/default.jpg";
        displayMessage(data.username, data.message, profilePic, data.timestamp);
                }
            };

            ws.onerror = () => console.error("WebSocket Error");
            ws.onclose = () => setTimeout(connectWebSocket, 3000); // Auto-reconnect
        }
        
        function handleKeyPress(event) {
    console.log("Key Pressed:", event.keyCode); // Debug key press event
    if (event.keyCode === 13) {
        sendMessage();
    }
}

function sendMessage() {
    let input = document.getElementById("message-input");
    let message = input.value.trim();
    if (message !== "") {
        let username = "<?php echo $username; ?>";
        let timestamp = new Date().toISOString().slice(0, 19).replace("T", " ");
        let profilePic = "img/default.png"; 
        let data = { username, message, profilePic, timestamp };

        console.log("Sending Message:", data); // Debug di console

        try {
            ws.send(JSON.stringify(data)); // Cuba hantar ke server
            console.log("Message sent successfully!");
        } catch (error) {
            console.error("WebSocket send error:", error);
        }

        input.value = "";
            }
        }
        
        function displayMessage(sender, message, profilePic, timestamp) {
            let chatBox = document.getElementById("chat-box");
            let msgElement = document.createElement("div");
            msgElement.classList.add("chat-message", sender === "<?php echo $username; ?>" ? "sent" : "received");

            let isSender = sender === "<?php echo $username; ?>";
            let profileImage = isSender ? "" : `<img src="${profilePic}" class="chat-avatar" onerror="this.onerror=null; this.src='img/default.jpg';">`;
            
            msgElement.innerHTML = `
                ${profileImage}
                <div class="chat-bubble">
                    <div class="chat-content">
                        <strong>${sender}</strong>
                        <p>${message}</p>
                        <span class="chat-timestamp">${timestamp}</span>
                    </div>
                </div>
            `;

            chatBox.appendChild(msgElement);
            chatBox.scrollTop = chatBox.scrollHeight;
        }
        
        window.onload = function () {
            connectWebSocket();
            let chatHistory = <?php echo json_encode($chatHistory); ?>;
            chatHistory.forEach(data => {
                let msgKey = data.username + ":" + data.message + ":" + data.timestamp;
                if (!messageLog.has(msgKey)) {
                    messageLog.add(msgKey);
                    let profilePic = data.profile_pic || "img/default.jpg";
                    displayMessage(data.username, data.message, profilePic, data.timestamp);
                }
            });
        };
    </script>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">Community Chat</div>
        <div id="chat-box" class="chat-box"></div>
        <div class="chat-input">
        <input type="text" id="message-input" placeholder="Type a message..." onkeypress="handleKeyPress(event)">
        <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <nav class="bottom-nav">
        <a href="homepage_cust.php"><i class="fas fa-home"></i></a>
        <a href="community.php" class="active"><i class="fas fa-users"></i></a>
        <a href="pets.php"><i class="fas fa-paw"></i></a>
        <a href="profile_cust.php"><i class="fas fa-user"></i></a>
    </nav>
</body>
</html>
