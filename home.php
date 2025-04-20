<?php
// index.php - Main file for the veterinary website
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinary Website</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="services.php">Veterinary Services</a></li>
                <li><a href="appointment.php">Request Appointments</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <section class="grid">
            <div class="card">
                <img src="vet1.jpg" alt="Our Story">
                <h3>Our Story</h3>
            </div>
            <div class="card">
                <img src="vet2.jpg" alt="Ask The Vet">
                <h3>Ask The Vet</h3>
            </div>
            <div class="card">
                <img src="vet3.jpg" alt="Careers">
                <h3>Careers</h3>
            </div>
            <div class="card">
                <img src="vet4.jpg" alt="Training & Development">
                <h3>Training & Development</h3>
            </div>
        </section>
        
        <section class="services">
            <img src="images/pet-care1.jpg" alt="24/7 Pet Care">
            <img src="images/pet-care2.jpg" alt="Vet Consultation">
            <img src="images/pet-care3.jpg" alt="Advanced Treatment">
            <img src="images/pet-care4.jpg" alt="X-ray Services">
        </section>
    </main>
</body>
</html>
