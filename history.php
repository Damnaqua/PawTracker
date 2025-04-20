<?php
session_start();

// Pastikan user dah login
if (!isset($_SESSION['user_id'])) {
    header("Location: member_login.php");
    exit();
}

// Koneksi database
$mysqli = new mysqli("localhost", "root", "", "alynnvet");

// Semak jika ada error dalam koneksi
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// **[1] Dapatkan ID vet yang sedang login**
$user_id = $_SESSION['user_id'];

// **[2] Ambil semua appointment berdasarkan vet_id**
$query = "SELECT a.*, p.name, v.vet_name 
          FROM appointments a
          JOIN pets p ON a.pet_id = p.id
          JOIN vets v ON a.vet_id = v.vet_id
          WHERE p.owner_id = ? 
          ORDER BY a.appointment_date DESC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


// **[3] Simpan data appointment dalam array**
$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment History</title>
    <link rel="stylesheet" href="history.css">
</head>
<body>

<div class="container">
<a href="homepage_cust.php" class="back-button">← Back</a>
    <h2>Appointment History</h2>
    
    <div class="tabs">
        <button class="tab-btn active" onclick="showTab('upcoming')">Upcoming</button>
        <button class="tab-btn" onclick="showTab('past')">Past</button>
    </div>

    <div id="upcoming" class="tab-content">
        <?php 
        $hasUpcoming = false;
        foreach ($appointments as $appt): 
            if (strtotime($appt['appointment_date']) >= strtotime(date("Y-m-d"))): 
                $hasUpcoming = true;
        ?>
            <div class="appointment-card">
    <h3><?php echo htmlspecialchars($appt['name']); ?></h3>
    <p>Vet: Dr. <?php echo htmlspecialchars($appt['vet_name']); ?></p>
    <p>Date: <?php echo htmlspecialchars($appt['appointment_date']); ?></p>
    <p>Time: <?php echo htmlspecialchars($appt['appointment_time']); ?></p>
    <p class="status <?php echo strtolower($appt['status']); ?>">
        <?php echo ucfirst($appt['status']); ?>
    </p>
</div>

        <?php 
            endif;
        endforeach;
        if (!$hasUpcoming) echo "<p>No upcoming appointments.</p>";
        ?>
    </div>

    <div id="past" class="tab-content" style="display:none;">
        <?php 
        $hasPast = false;
        foreach ($appointments as $appt): 
            if (strtotime($appt['appointment_date']) < strtotime(date("Y-m-d"))): 
                $hasPast = true;
        ?>
            <div class="appointment-card">
                <h3><?php echo htmlspecialchars($appt['pet_id']); ?></h3>
                <p>Vet: <?php echo htmlspecialchars($appt['vet_id']); ?></p>
                <p>Date: <?php echo htmlspecialchars($appt['appointment_date']); ?></p>
                <p>Time: <?php echo htmlspecialchars($appt['appointment_time']); ?></p>
                <p class="status <?php echo strtolower($appt['status']); ?>">
                    <?php echo ucfirst($appt['status']); ?>
                </p>
            </div>
        <?php 
            endif;
        endforeach;
        if (!$hasPast) echo "<p>No past appointments.</p>";
        ?>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    showTab('upcoming'); // Pastikan tab upcoming ditunjukkan dulu
});

    
function showTab(tab) {
    document.getElementById("upcoming").style.display = (tab === 'upcoming') ? "block" : "none";
    document.getElementById("past").style.display = (tab === 'past') ? "block" : "none";

    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`button[onclick="showTab('${tab}')"]`).classList.add('active');
}

</script>

</body>
</html>
