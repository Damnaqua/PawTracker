<?php
session_start();
include 'dbconnect.php'; // Ensure database connection

// Initialize an empty array for storing appointments
$appointments = [];

// Fetch appointments from the database
$query = "
    SELECT appointment_date, appointment_time, appointment_type 
    FROM appointments 
    WHERE WEEK(appointment_date, 1) = WEEK(NOW(), 1) 
    AND YEAR(appointment_date) = YEAR(NOW())
";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row; // Store each appointment in the array
    }
} else {
    echo "Error fetching appointments: " . mysqli_error($conn);
}

// Function to assign colors based on appointment type
function getAppointmentColor($type) {
    $appointmentColors = [
        'deworming' => 'green',
        'vaccination' => 'yellow',
        'general checkup' => 'orange',
        'consultation' => 'blue'
    ];
    return $appointmentColors[trim(strtolower($type))] ?? 'gray';
}

// Define time slots (e.g., 10:00am - 5:00pm)
$timeSlots = [
    '10:00am', '11:00am', '12:00pm', '1:00pm', '2:00pm', '3:00pm', '4:00pm', '5:00pm'
];

// Define week days
$weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="calendar.css">
</head>
<body>

<div class="container">
    <aside class="sidebar">
        <h2>PawTracker</h2>
        <nav>
            <ul>
                <li><a href="dashboard.php">Overview</a></li>
                <li><a href="patients.php">Patients</a></li>
                <li><a href="staff.php">Staff</a></li>
                <li><a href="status.php">Status</a></li>
                <li class="active"><a href="calendar.php">Calendar</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="announce.php">Announcement</a></li>
            </ul>
        </nav>
    </aside>

    <main class="calendar-container">
        <h1>Calendar</h1>
        <table class="calendar">
            <tr>
                <th>Time</th>
                <?php foreach ($weekDays as $day) { echo "<th>$day</th>"; } ?>
            </tr>
            
            <?php
            foreach ($timeSlots as $time) {
                echo "<tr>";
                echo "<td>$time</td>";
                
                foreach ($weekDays as $day) {
                    $found = false;
                    
                    foreach ($appointments as $appointment) {
                        $appointmentDay = date('D', strtotime($appointment['appointment_date']));
                        $appointmentTime = date('g:ia', strtotime($appointment['appointment_time']));
                        
                        if ($appointmentDay === $day && $appointmentTime === strtolower($time)) {
                            $color = getAppointmentColor($appointment['appointment_type']);
                            echo "<td class='appointment $color'>" . htmlspecialchars($appointment['appointment_type']) . "</td>";
                            $found = true;
                            break;
                        }
                    }
                    
                    if (!$found) {
                        echo "<td></td>";
                    }
                }
                echo "</tr>";
            }
            ?>
        </table>
    </main>
</div>
</body>
</html>
