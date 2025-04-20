<?php
// services.php - Veterinary Services Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - Veterinary Clinic</title>
    <link rel="stylesheet" href="services.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="appointment.php">Request Appointments</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="services-container">
            <h2>Our Veterinary Services</h2>
            <div class="grid">
                <div class="card">
                    <img src="images/general-checkup.jpg" alt="General Check-up">
                    <h3>General Check-up</h3>
                    <p>Routine health assessments to ensure your pet's well-being.</p>
                </div>
                <div class="card">
                    <img src="images/vaccination.jpg" alt="Vaccinations">
                    <h3>Vaccinations</h3>
                    <p>Protect your pet with timely vaccinations and prevent diseases.</p>
                </div>
                <div class="card">
                    <img src="images/dental-care.jpg" alt="Dental Care">
                    <h3>Dental Care</h3>
                    <p>Comprehensive dental check-ups and cleaning for healthy teeth.</p>
                </div>
                <div class="card">
                    <img src="images/surgery.jpg" alt="Surgical Services">
                    <h3>Surgical Services</h3>
                    <p>Advanced surgical procedures performed by experienced veterinarians.</p>
                </div>
                <div class="card">
                    <img src="images/emergency.jpg" alt="Emergency Care">
                    <h3>Emergency Care</h3>
                    <p>24/7 emergency services for critical pet health situations.</p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
