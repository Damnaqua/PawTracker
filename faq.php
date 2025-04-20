<?php
session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQs - PawTracker</title>
    <link rel="stylesheet" href="faq.css">
</head>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const faqs = document.querySelectorAll(".faq-item");

        faqs.forEach(faq => {
            faq.addEventListener("click", function () {
                this.classList.toggle("active");
            });
        });
    });
</script>

<body>

    <!-- Navbar -->
    <nav>
    <div class="logo">
    <a href="homepage_cust.php" class="logo-link"> 
    <img src="img/logo.png" alt="PawTracker Logo" class="logo-img">
    <span class="logo-text">PAWTRACKER</span></a>
</div>

    </nav>

    <!-- FAQ Section -->
    <section class="faq">
        <h1>Frequently Asked Questions</h1>
        <p>Find answers to common questions about PawTracker.</p>

        <div class="faq-container">
            <div class="faq-item">
                <button class="faq-question">What is PawTracker? <span class="toggle-icon">+</span></button>
                <div class="faq-answer">
                    <p>PawTracker is a pet health management system that allows pet owners to book appointments, track pet health records, and access veterinary services easily.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">How do I book an appointment? <span class="toggle-icon">+</span></button>
                <div class="faq-answer">
                    <p>You can book an appointment through the <a href="appointment.php">Appointments</a> page by selecting a date, time, and preferred veterinarian.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">Is my pet’s data secure? <span class="toggle-icon">+</span></button>
                <div class="faq-answer">
                    <p>Yes, we prioritize the security and privacy of your pet’s health records using encrypted data storage.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">Can I cancel or reschedule my appointment? <span class="toggle-icon">+</span></button>
                <div class="faq-answer">
                    <p>Yes, you can manage your appointments in the profile section or contact your veterinarian directly for changes.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">How do I contact customer support? <span class="toggle-icon">+</span></button>
                <div class="faq-answer">
                    <p>You can reach our support team via email at support@pawtracker.com or through the contact section in your profile.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 PawTracker. All Rights Reserved.</p>
    </footer>

    <script>
        // Toggle FAQ Answers
        const questions = document.querySelectorAll(".faq-question");

        questions.forEach(question => {
            question.addEventListener("click", function() {
                const answer = this.nextElementSibling;
                const icon = this.querySelector(".toggle-icon");

                if (answer.style.display === "block") {
                    answer.style.display = "none";
                    icon.textContent = "+";
                } else {
                    answer.style.display = "block";
                    icon.textContent = "-";
                }
            });
        });

        // Toggle mobile menu
        document.querySelector(".menu-toggle").addEventListener("click", function() {
            document.querySelector(".nav-links").classList.toggle("active");
        });
    </script>

</body>
</html>
