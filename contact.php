<?php
// contact.php - Contact Page for Veterinary Clinic

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $subject = htmlspecialchars($_POST["subject"]);
    $message = htmlspecialchars($_POST["message"]);

    // Here, you would typically process and send the data via email or store it in a database
    echo "<script>alert('Message sent successfully! We will get back to you soon.');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Veterinary Clinic</title>
    <link rel="stylesheet" href="contact.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="appointment.php">Request Appointment</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="contact-container">
            <h2>Contact Us</h2>
            <p>If you have any questions, feel free to reach out using the form below.</p>
            <form action="contact.php" method="POST">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required>

                <label for="subject">Subject:</label>
                <input type="text" id="subject" name="subject" required>

                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="5" required></textarea>

                <button type="submit">Send Message</button>
            </form>
        </section>

        <section class="contact-info">
            <h3>Our Contact Information</h3>
            <p><strong>Address:</strong> 123 Pet Street, Veterinary City, VC 45678</p>
            <p><strong>Phone:</strong> +1 234 567 8900</p>
            <p><strong>Email:</strong> info@vetclinic.com</p>
            <p><strong>Hours:</strong> Mon-Fri: 8am - 6pm | Sat: 9am - 4pm</p>
        </section>
    </main>
</body>
</html>
