<?php
session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - PawTracker</title>
    <link rel="stylesheet" href="about.css">
</head>
<body>

    <!-- Navbar -->
    <nav>
        <a href="homepage_cust.php" class="logo-link"> 
    <img src="img/logo.png" alt="PawTracker Logo" class="logo-img">
    <span class="logo-text">PAWTRACKER</span></a>
        <div class="menu-toggle">&#9776;</div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>About PawTracker</h1>
            <p>Helping pet owners provide the best care with ease.</p>
        </div>
    </section>

    <!-- About Section -->
    <section class="about">
        <div class="about-container">
            <div class="about-text">
                <h2>Who We Are</h2>
                <p>PawTracker is a pet health management platform that allows pet owners to track their pet’s health records, book veterinary appointments, and access important pet care resources.</p>
                <p>We strive to make pet care convenient and accessible for everyone.</p>
            </div>
            <div class="about-image">
                <img src="img/logo.png" alt="Pet Care">
            </div>
        </div>
    </section>

    <!-- Our Mission Section -->
    <section class="mission">
        <div class="mission-container">
            <h2>Our Mission</h2>
            <p>To create a <b>seamless</b> and <b>reliable</b> pet health management system that empowers pet owners and veterinarians to work together for better pet well-being.</p>
            <div class="mission-stats">
                <div class="stat">
                    <h3>10K+</h3>
                    <p>Happy Pet Owners</p>
                </div>
                <div class="stat">
                    <h3>500+</h3>
                    <p>Trusted Veterinarians</p>
                </div>
                <div class="stat">
                    <h3>5K+</h3>
                    <p>Appointments Booked</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Meet the Team -->
    <section class="team">
        <h2>Meet Our Team</h2>
        <div class="team-container">
            <div class="team-member">
                <img src="img/vet.jpg" alt="Dr. Leslie">
                <h3>Dr. Leslie Alexander</h3>
                <p>Veterinarian</p>
            </div>
            <div class="team-member">
                <img src="img/vet2.jpg" alt="Dr. Richard">
                <h3>Dr. Richard Miles</h3>
                <p>Veterinary Surgeon</p>
            </div>
            <div class="team-member">
                <img src="img/vet3.jpg" alt="Dr. Emma">
                <h3>Dr. Emma Roberts</h3>
                <p>Pet Nutritionist</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 PawTracker. All Rights Reserved.</p>
    </footer>

    <script>
        // Toggle mobile menu
        document.querySelector(".menu-toggle").addEventListener("click", function() {
            document.querySelector(".nav-links").classList.toggle("active");
        });
    </script>

</body>
</html>
