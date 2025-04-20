<?php
include 'dbconnect.php'; // Ensure this file contains your database connection logic

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    
    // Get the status selected by the user and map it to 'Yes' or 'No' for on_duty
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    if ($status == "Available") {
        $on_duty = "Yes"; // If "Available" is selected, save "Yes"
    } else {
        $on_duty = "No"; // If "On Duty" is selected, save "No"
    }

    // Insert the staff data into the database
    $query = "INSERT INTO vets (vet_name, specialization, on_duty) VALUES ('$name', '$role', '$on_duty')";
    
    if (mysqli_query($conn, $query)) {
        header("Location: staff.php"); // Redirect to staff list after successful insertion
        exit();
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
    <title>Add Staff</title>
    <link rel="stylesheet" href="add_staff.css"> <!-- Link to CSS file -->
</head>
<body>
    <div class="container">
        <h2>Add New Staff</h2>
        <form method="POST" action="">
            <label for="name">Staff Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="role">Job Title:</label>
            <input type="text" id="role" name="role" required>

            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="Available">Available</option>
                <option value="On Duty">On Duty</option>
            </select>

            <button type="submit">Add Staff</button>
        </form>
    </div>
</body>
</html>
