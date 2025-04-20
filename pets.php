<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: member_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$mysqli = new mysqli("localhost", "root", "", "alynnvet");

// Ambil senarai pets user
$pets = $mysqli->query("SELECT id, name FROM pets WHERE owner_id = '$user_id'");

// Default pet (first pet in list)
$selected_pet = null;
$pet_details = null;
$latest_vet_visit = '-';

if ($pets->num_rows > 0) {
    $selected_pet = $pets->fetch_assoc()['id'];
}

// Bila user pilih pet lain
if (isset($_POST['pet_id'])) {
    $selected_pet = $_POST['pet_id'];
}

// Ambil details pet yang dipilih
if ($selected_pet) {
    $pet_details = $mysqli->query("SELECT * FROM pets WHERE id = '$selected_pet'")->fetch_assoc();
    
    // Ambil latest vet visit
    $vet_visit_query = $mysqli->query("SELECT appointment_date FROM appointments WHERE pet_id = '$selected_pet' ORDER BY appointment_date DESC LIMIT 1");
if ($vet_visit_query->num_rows > 0) {
    $latest_vet_visit = $vet_visit_query->fetch_assoc()['appointment_date'];
}

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Pets | PawTracker</title>
    <link rel="stylesheet" href="pets.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

    <div class="container">
        <div class="pet-profile">
            <!-- Dropdown untuk pilih pet -->
            <form method="POST">
                <select name="pet_id" onchange="this.form.submit()">
                    <?php
                    $pets->data_seek(0);
                    while ($pet = $pets->fetch_assoc()) {
                        $selected = ($pet['id'] == $selected_pet) ? "selected" : "";
                        echo "<option value='{$pet['id']}' $selected>{$pet['name']}</option>";
                    }
                    ?>
                </select>
            </form>
            <div class="pet-image"></div>
            <div class="pet-name">
                <strong><?php echo $pet_details ? $pet_details['name'] : 'No Pet Selected'; ?></strong>
            </div>
        </div>

            <div class="record-item vet-visit">
                Recent Vet Visit <span>[<?php echo $latest_vet_visit; ?>]</span>
            </div>
        </div>
    </div>

    <nav class="bottom-nav">
        <a href="homepage_cust.php"><i class="fas fa-home"></i></a>
        <a href="community.php"><i class="fas fa-users"></i></a>
        <a href="pets.php" class="active"><i class="fas fa-paw"></i></a>
        <a href="profile_cust.php"><i class="fas fa-user"></i></a>
    </nav>

</body>
</html>
