<?php
include 'dbconnect.php';

if (isset($_POST['date'])) {
    $selectedDate = $_POST['date'];
    
    $query = "SELECT * FROM appointments WHERE DATE(appointment_date) = DATE('$selectedDate') ORDER BY appointment_time ASC";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "<p class='error'>Database error: " . mysqli_error($conn) . "</p>";
        exit;
    }

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $time = date("h:i A", strtotime($row['appointment_time']));
            $type = !empty($row['appointment_type']) ? $row['appointment_type'] : "General Checkup";

            // Debug
            error_log("Appointment Found: Time - $time, Type - $type");

            $colorClass = "default";
            switch ($type) {
                case "General Checkup": $colorClass = "yellow"; break;
                case "Vaccination": $colorClass = "blue"; break;
                case "Deworming": $colorClass = "green"; break;
                case "Consultation": $colorClass = "orange"; break;
            }

            echo "<div class='appointment-card $colorClass'>
                    <span class='appointment-time'>$time</span>
                    <span class='appointment-details'>$type</span>
                  </div>";
        }
    } else {
        echo "<p class='no-appointments'>No appointments for this date.</p>";
    }
}
?>