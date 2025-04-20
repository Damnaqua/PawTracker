<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: member_login.php");
    exit();
}
if ($_SESSION['role'] != 1) {
    header("Location: dashboard.php");
    exit();
}
include 'dbconnect.php';

// Check overdue vaccine appointments
$reminderMessage = "";
$sql = "SELECT pets.name FROM pets 
        LEFT JOIN appointments ON pets.id = appointments.pet_id 
        AND appointments.appointment_type = 'Vaccine' 
        ORDER BY appointments.appointment_date DESC";
$result = $conn->query($sql);

$now = new DateTime();
$reminders = [];
while ($row = $result->fetch_assoc()) {
    $lastAppointmentDate = new DateTime($row['last_date'] ?? '2000-01-01');
    $interval = $lastAppointmentDate->diff($now);
    if ($interval->y >= 1) {
        $reminders[] = [
            'message' => "Your pet, " . $row['name'] . ", hasn't scheduled an annual vaccine!"
        ];
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home | PawTracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="homepage_cust.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .notification-container {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 999;
        }

        .notification-icon {
            position: relative;
            font-size: 20px;
            cursor: pointer;
            font-family: 'Lucida Console';
        }

        .notification-icon .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            font-size: 12px;
            border-radius: 50%;
            padding: 5px;
        }

        .notification-panel {
            display: none;
            position: absolute;
            right: 0;
            top: 30px;
            width: 300px;
            border-radius: 10px;
            background: white;
            border: 1px solid #ccc;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 10px;
            z-index: 1000;
        }

        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .notification-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- NOTIFICATION moved outside banner -->
    <div class="notification-container">
        <div class="notification-icon" id="notifIcon">
            <i class="fas fa-bell"></i>
            <?php if (count($reminders) > 0): ?>
                <span class="badge" id="notifBadge">!</span>
            <?php else: ?>
                <span class="badge" id="notifBadge" style="display:none;">!</span>
            <?php endif; ?>
        </div>
        <div class="notification-panel" id="notifPanel">
            <?php if (count($reminders) == 0): ?>
                <p><strong>No urgent appointments.</strong></p>
            <?php else: ?>
                <div id="notifContent">
                    <?php foreach ($reminders as $item): ?>
                        <div class='notification-item'>⚠️ <?= $item['message'] ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="banner">
        <div class="slideshow-container">
            <img class="slide active" src="img/banner.png" alt="Banner 1">
            <img class="slide" src="img/banner2.png" alt="Banner 2">
            <img class="slide" src="img/banner3.png" alt="Banner 3">
        </div>
    </div>

    <div class="buttons">
        <button onclick="location.href='appointment.php'">Appointment</button>
        <button onclick="location.href='history.php'">History</button>
        <button onclick="location.href='faq.php'">FAQs</button>
        <button onclick="location.href='about.php'">About</button>
    </div>
    
    <div class="products-section">
    <h2>🧼 Product We Use</h2>
    <div class="product-list">
        <div class="product-card">
            <img src="img/shampoo.png" alt="Shampoo">
            <div class="product-info">
                <h3>Gentle Pet Shampoo</h3>
                <p>Specially formulated to clean pet fur without irritating sensitive skin. Keeps coat soft and shiny.</p>
            </div>
        </div>
        <div class="product-card">
            <img src="img/conditioner.jpg" alt="Conditioner">
            <div class="product-info">
                <h3>Soothing Conditioner</h3>
                <p>Used to detangle fur and reduce shedding. Contains aloe vera to nourish skin.</p>
            </div>
        </div>
        <div class="product-card">
            <img src="img/spray.jpg" alt="Anti-Tick Spray">
            <div class="product-info">
                <h3>Anti-Tick Spray</h3>
                <p>Protects pets from ticks and fleas after grooming. Made with pet-safe ingredients.</p>
            </div>
        </div>
        <div class="product-card">
            <img src="img/cleaner.jpg" alt="Ear Cleaner">
            <div class="product-info">
                <h3>Ear Cleaning Solution</h3>
                <p>Prevents ear infections by gently removing wax and debris during grooming sessions.</p>
            </div>
        </div>
    </div>
</div>

</div>


    <nav class="bottom-nav">
        <a href="homepage_cust.php" class="active"><i class="fas fa-home"></i></a>
        <a href="community.php"><i class="fas fa-users"></i></a>
        <a href="pets.php"><i class="fas fa-paw"></i></a>
        <a href="profile_cust.php"><i class="fas fa-user"></i></a>
    </nav>
</div>

<script>
    $(document).ready(function () {
        $('#notifIcon').click(function () {
            $('#notifPanel').toggle();
        });

        let currentSlide = 0;
        const slides = document.querySelectorAll(".slide");

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove("active");
                slide.style.opacity = "0";
                if (i === index) {
                    slide.classList.add("active");
                    slide.style.opacity = "1";
                }
            });
        }

        setInterval(() => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }, 3000);
    });
</script>

</body>
</html>
