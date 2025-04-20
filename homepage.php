<?php
session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawTracker - Home</title>
    <link rel="stylesheet" href="homepage.css">
</head>
<body>

    <!-- Navbar -->
    <nav>
    <div class="logo-container">
    <img src="img/logo.png" alt="PawTracker Logo" class="logo">
    <span class="logo-text">PawTracker</span>
</div>
        <div class="menu-toggle">&#9776;</div>
        <ul class="nav-links">
            <li><a href="homepage.php">Home</a></li>
            <li><a href="appointment.php">Appointments</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="faq.php">FAQs</a></li>
        </ul>
    </nav>

    <!-- Hero Section -->
    <header>
        <h1>Welcome to PawTracker</h1>
        <p>Your trusted pet health management system</p>
        <a href="appointment.php" class="btn">Book an Appointment</a>
    </header>

    <!-- Services Section -->
    <section class="services">
        <h2>Our Services</h2>
        <div class="service-container">
            <div class="service-box">
                <img src="img/checkup.png" alt="Checkup">
                <h3>Pet Checkup</h3>
                <p>Regular health checkups to keep your pet in top shape.</p>
            </div>
            <div class="service-box">
                <img src="img/vaccination.png" alt="Vaccination">
                <h3>Vaccination</h3>
                <p>Essential vaccinations to protect your pet from diseases.</p>
            </div>
            <div class="service-box">
                <img src="img/grooming.png" alt="Grooming">
                <h3>Grooming</h3>
                <p>Keep your pet clean and stylish with our grooming services.</p>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <h2>What Our Customers Say</h2>
        <div class="testimonial">
            <p>"PawTracker has made booking vet appointments so easy. My dog loves their services!"</p>
            <span>- Emily R.</span>
        </div>
        <div class="testimonial">
            <p>"The best pet care service in town. Highly recommend!"</p>
            <span>- Michael T.</span>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 PawTracker. All Rights Reserved.</p>
    </footer>

    <script>
        // Toggle menu for mobile view
        document.querySelector(".menu-toggle").addEventListener("click", function() {
            document.querySelector(".nav-links").classList.toggle("active");
        });
    </script>

</body>
</html>
