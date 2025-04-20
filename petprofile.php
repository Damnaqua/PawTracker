<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: member_login.php");
    exit();
}


$mysqli = new mysqli("localhost", "root", "", "alynnvet");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$user_id = $_SESSION['user_id'];

// **Ambil senarai pets berdasarkan user_id**
// **Ambil senarai pets berdasarkan user_id**
$pets = [];
$result = $mysqli->query("SELECT * FROM pets WHERE owner_id = '$user_id'");

if ($result === false) {
    die("SQL Error: " . $mysqli->error); // Kalau query error
}

while ($row = $result->fetch_assoc()) {
    $pets[] = $row; // Masukkan semua pets ke dalam array
}


// **Tambah pet baru**
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pet_name = $mysqli->real_escape_string($_POST['pet_name']);
    $species = $mysqli->real_escape_string($_POST['species']);
    $breed = $mysqli->real_escape_string($_POST['breed']);
    $age = intval($_POST['age']);
    $gender = $mysqli->real_escape_string($_POST['gender']);
    
    $sql = "INSERT INTO pets (owner_id, name, species, breed, age, gender) 
            VALUES ('$user_id', '$pet_name', '$species', '$breed', '$age', '$gender')";

    if ($mysqli->query($sql)) {
        header("Location: petprofile.php"); // Refresh page lepas tambah pet
        exit();
    } else {
        echo "Error: " . $mysqli->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Profile</title>
    <link rel="stylesheet" href="petprofile.css">
</head>
<body>

<div class="container">
<a href="profile_cust.php" class="back-button">← Back</a>
    <h2>My Pets</h2>

    <div class="pet-list">
        <?php if (count($pets) > 0): ?>
            <ul>
                <?php foreach ($pets as $pet): ?>
                    <li>
                        <h3><?php echo htmlspecialchars($pet['name']); ?></h3>
                        <p>Species: <?php echo htmlspecialchars($pet['species']); ?></p>
                        <p>Breed: <?php echo htmlspecialchars($pet['breed']); ?></p>
                        <p>Age: <?php echo $pet['age']; ?> years</p>
                        <p>Gender: <?php echo htmlspecialchars($pet['gender']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No pets added yet.</p>
        <?php endif; ?>
    </div>

    <a href="add_pet.php"><button>Add New Pet</button></a>
</div>

</body>
</html>
