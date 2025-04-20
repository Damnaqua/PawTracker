<?php
session_start();
include 'dbconnect.php';

// Fetch owners for the dropdown
$ownersQuery = "SELECT id, username FROM users ORDER BY username ASC";
$ownersResult = mysqli_query($conn, $ownersQuery);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $species = mysqli_real_escape_string($conn, $_POST['species']);
    $breed = mysqli_real_escape_string($conn, $_POST['breed']);
    $age = intval($_POST['age']);
    $owner_id = intval($_POST['owner_id']);

    // Insert into patients table
    $query = "INSERT INTO patients (name, species, breed, age, owner_id) 
              VALUES ('$name', '$species', '$breed', '$age', '$owner_id')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Patient added successfully!'); window.location='patients.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Patient</title>
    <link rel="stylesheet" href="add_patient.css">
</head>
<body>
    <div class="form-container">
        <h2>Add New Patient</h2>
        <form method="post" action="">
            <label for="name">Patient Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="species">Species:</label>
            <input type="text" id="species" name="species" required>

            <label for="breed">Breed:</label>
            <input type="text" id="breed" name="breed">

            <label for="age">Age:</label>
            <input type="number" id="age" name="age" min="0" required>

            <label for="owner_id">Owner:</label>
            <select id="owner_id" name="owner_id" required>
                <option value="">Select Owner</option>
                <?php while ($row = mysqli_fetch_assoc($ownersResult)) { ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['username']; ?></option>
                <?php } ?>
            </select>

            <button type="submit">Add Patient</button>
        </form>
        <a href="patients.php" class="back-link">← Back to Patients</a>
    </div>
</body>
</html>
