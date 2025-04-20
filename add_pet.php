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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pet_name = $mysqli->real_escape_string($_POST['pet_name']);
    $species = $mysqli->real_escape_string($_POST['species']);
    $breed = $mysqli->real_escape_string($_POST['breed']);
    $age = intval($_POST['age']);
    $gender = $mysqli->real_escape_string($_POST['gender']);

    $sql = "INSERT INTO pets (owner_id, name, species, breed, age, gender) 
            VALUES ('$user_id', '$pet_name', '$species', '$breed', '$age', '$gender')";

    if ($mysqli->query($sql)) {
        header("Location: petprofile.php"); // Redirect balik ke list pet
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
    <title>Add New Pet</title>
    <link rel="stylesheet" href="petprofile.css">
</head>
<body>

<div class="container">
    <h2>Add New Pet</h2>
    <form action="add_pet.php" method="post">
        <label for="pet_name">Pet Name:</label>
        <input type="text" id="pet_name" name="pet_name" required>

        <label for="species">Species:</label>
        <input type="text" id="species" name="species" required>

        <label for="breed">Breed:</label>
        <input type="text" id="breed" name="breed" required>

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" min="0" required>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <button type="submit">Add Pet</button>
    </form>

    <!-- Button balik ke petprofile -->
    <a href="petprofile.php"><button>Back to Pet List</button></a>

</div>

</body>
</html>
