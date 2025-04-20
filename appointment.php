<?php
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');
include 'dbconnect.php';

// Ambil senarai haiwan peliharaan pengguna
$owner_id = $_SESSION['user_id'];
$query = "SELECT id, name FROM pets WHERE owner_id = ?";
$pets = [];
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $pets[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book an Appointment</title>
    <link rel="stylesheet" href="appointment.css">
</head>
<body>

<div class="appointment-container">
    <div class="appointment-left"> 
        <a href="homepage_cust.php" class="previous round">&#8249;</a>
        <h2>BOOK AN APPOINTMENT</h2>

        <div class="doctor-info">
            <img src="img/doctor.jpg" alt="Doctor">
            <div>
                <h3>Dr. Name</h3>
                <p>Veterinary Specialist</p>
            </div>
        </div>

        <div class="clinic-info">
            <img src="clinic.png" alt="Clinic">
            <div>
                <h3>Veterinary Clinic</h3>
                <p>Jalan Sukarelawan 1</p>
            </div>
        </div>

        <div class="calendar-container">
            <div class="calendar-header">
                <button id="prevMonth">&lt;</button>
                <span id="currentMonth"></span>
                <button id="nextMonth">&gt;</button>
            </div>
            <div class="calendar-grid" id="calendarGrid"></div>
        </div>
    </div>

    <div class="appointment-right">
        <div class="promo" style="font-family: Verdana">
            <h1>Don’t Delay,</h1>
            <p>Book Today!</p>
        </div>

        <form id="appointmentForm">
            <input type="hidden" name="owner_id" value="<?php echo $_SESSION['user_id']; ?>">
            <input type="hidden" id="selected_date" name="appointment_date">
            
            <label>Select a Time:</label>
            <div class="time-selection" id="timeSlots">
                <?php
                $times = ["10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00"];
                foreach ($times as $time) {
                    echo "<button type='button' class='time-slot' data-time='$time'>$time</button>";
                }
                ?>
            </div>

            <input type="hidden" id="selected_time" name="appointment_time">
            
            <label for="appointment_type">Appointment Type:</label>
            
            <select id="appointment_type" name="appointment_type" required>
            <option value="" disabled selected>Select Appointment</option>
                <option value="General Checkup">General Checkup</option>
                <option value="Vaccination">Vaccination</option>
                <option value="Deworming">Deworming</option>
                <option value="Consultation">Consultation</option>
            </select>

            <label for="pet_id">Select Pet:</label>
            <select id="pet_id" name="pet_id" required>
            <option value="" disabled selected>Select Pet</option>
            <?php foreach ($pets as $pet) { ?>
            <option value="<?= $pet['id']; ?>"><?= $pet['name']; ?></option>
            <?php } ?>
            </select>

            <button type="submit" class="book-btn">Book Appointment</button>
        </form>

        <div id="responseMessage"></div>
    </div>
    
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let currentDate = new Date();
    const calendarGrid = document.getElementById("calendarGrid");
    const currentMonth = document.getElementById("currentMonth");
    let selectedDate = null;
    let selectedTime = null;

    function updateCalendar() {
        calendarGrid.innerHTML = "";
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const firstDayOfMonth = new Date(year, month, 1).getDay();
        const today = new Date();

        currentMonth.textContent = new Intl.DateTimeFormat("en", { month: "long", year: "numeric" }).format(currentDate);

        // Kosongkan sebelum tarikh pertama bulan ini
        for (let i = 0; i < firstDayOfMonth; i++) {
            const emptyCell = document.createElement("div");
            emptyCell.classList.add("empty");
            calendarGrid.appendChild(emptyCell);
        }

        const daysOfWeek = ["MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN"];
        daysOfWeek.forEach(day => {
            const dayElement = document.createElement("div");
            dayElement.textContent = day;
            calendarGrid.appendChild(dayElement);
        });

        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement("div");
            dayElement.classList.add("calendar-day");
            dayElement.textContent = day;

            let dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            let dateObj = new Date(dateStr);

            if (dateObj < today.setHours(0, 0, 0, 0)) {
                dayElement.classList.add("disabled"); // Tarikh lepas disable
            } else {
                dayElement.addEventListener("click", function() {
                    document.querySelectorAll(".calendar-day").forEach(el => el.classList.remove("selected"));
                    dayElement.classList.add("selected");
                    selectedDate = dateStr;
                    document.getElementById("selected_date").value = selectedDate;
                });
            }
            calendarGrid.appendChild(dayElement);
        }
    }
    document.getElementById("prevMonth").addEventListener("click", function () {
        currentDate.setMonth(currentDate.getMonth() - 1);
        updateCalendar();
    });

    document.getElementById("nextMonth").addEventListener("click", function () {
        currentDate.setMonth(currentDate.getMonth() + 1);
        updateCalendar();
    });

    document.querySelectorAll(".time-slot").forEach(button => {
        button.addEventListener("click", function () {
            document.querySelectorAll(".time-slot").forEach(el => el.classList.remove("selected"));
            this.classList.add("selected");
            selectedTime = this.getAttribute("data-time");
            document.getElementById("selected_time").value = selectedTime;
        });
    });

    document.getElementById("appointmentForm").addEventListener("submit", function(event) {
        let selectedDate = document.getElementById("selected_date").value;
        let selectedTime = document.getElementById("selected_time").value;

        if (!selectedDate) {
            alert("Please select a date before booking.");
            event.preventDefault();
            return;
        }

        if (!selectedTime) {
            alert("Please select a time before booking.");
            event.preventDefault();
            return;
        }

        let formData = new FormData(this);

        fetch("process_appointment.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            let responseDiv = document.getElementById("responseMessage");
            responseDiv.innerHTML = data.message;
            responseDiv.style.color = data.success ? "green" : "red";
            if (data.success) {
                document.getElementById("appointmentForm").reset();
            }
        })
        .catch(error => console.error("Error:", error));

        event.preventDefault();
    });

    updateCalendar();
});
</script>

</body>
</html>
