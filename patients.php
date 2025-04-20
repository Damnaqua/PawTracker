<?php
session_start();
include 'dbconnect.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Query to get pet log history (appointment history)
$query = "SELECT a.id AS appointment_id, p.id AS pet_id, p.name AS pet_name, p.species, p.age, 
                 a.appointment_date, a.appointment_type,
                 a.status AS appointment_status, 
                 a.treatment, 
                 u.username AS owner_name, u.contact_number, 
                 v.vet_name
          FROM appointments a
          JOIN pets p ON a.pet_id = p.id
          JOIN users u ON p.owner_id = u.id
          LEFT JOIN vets v ON a.vet_id = v.vet_id
          WHERE u.role = 1
          ORDER BY a.appointment_date DESC, a.appointment_type DESC";


$result = mysqli_query($conn, $query);

// Handle form submission to update treatment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_treatment'])) {
    $appointment_id = $_POST['appointment_id'];
    $treatment = $_POST['treatment'];

    // Update the treatment field for the specific appointment_id
    $update_query = "UPDATE appointments SET treatment = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $treatment, $appointment_id);
    if ($stmt->execute()) {
        header("Location: patients.php?success=1");
        exit();
    } else {
        echo "Error updating treatment.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients</title>
    <link rel="stylesheet" href="patients.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>PawTracker</h2>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Overview</a></li>
                    <li class="active"><a href="patients.php">Patients</a></li>
                    <li><a href="staff.php">Staff</a></li>
                    <li><a href="status.php">Status</a></li>
                    <li><a href="calendar.php">Calendar</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="announce.php">Announcement</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header>
                <h1>Pet Log History</h1>
                <a href="add_patient.php" class="add-patient">+ Add Pet Log</a>
            </header>

            <div class="search-container">
                <input type="text" id="search" placeholder="Search for pet logs...">
                <button id="search-btn">🔍</button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Pet’s Name</th>
                        <th>Owner</th>
                        <th>Contact Number</th>
                        <th>Vet</th>
                        <th>Remarks</th>
                        <th>Appointment Type</th>
                        <th>Visit Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="patient-table">
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr id="row-<?php echo $row['appointment_id']; ?>">
                            <td><?php echo htmlspecialchars($row['pet_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['owner_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['vet_name']); ?></td>

                            <!-- Editable treatment column -->
                            <td class="editable">
                                <span class="view-mode"><?php echo htmlspecialchars($row['treatment'] ?? ''); ?></span>
                                <input type="text" class="edit-mode" value="<?php echo htmlspecialchars($row['treatment'] ?? ''); ?>" style="display: none;">
                            </td>

                            <td><?php echo htmlspecialchars($row['appointment_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>

                            <!-- Action column with Edit and Save buttons -->
                            <td>
                                <button class="edit-btn" onclick="enableEdit(<?php echo $row['appointment_id']; ?>)">Edit</button>
                                <form method="POST" style="display: none;" class="save-form" id="save-form-<?php echo $row['appointment_id']; ?>">
                                    <input type="hidden" name="appointment_id" value="<?php echo $row['appointment_id']; ?>">
                                    <input type="text" name="treatment" class="edit-mode" value="<?php echo htmlspecialchars($row['treatment'] ?? ''); ?>">
                                    <button type="submit" name="update_treatment">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="pagination">
                <a href="#">«</a>
                <a href="#" class="active">1</a>
                <a href="#">2</a>
                <a href="#">3</a>
                <a href="#">4</a>
                <a href="#">5</a>
                <a href="#">»</a>
            </div>
        </main>
    </div>

    <script>
        $(document).ready(function () {
            $("#search").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#patient-table tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        });

        // Enable editing directly in the table and show Save button
        function enableEdit(appointment_id) {
            // Toggle visibility of view-mode and edit-mode
            document.querySelector(`#row-${appointment_id} .view-mode`).style.display = "none";
            document.querySelector(`#row-${appointment_id} .edit-mode`).style.display = "inline-block";
            document.querySelector(`#save-form-${appointment_id}`).style.display = "block";

            // Hide Edit button after clicking
            document.querySelector(`#row-${appointment_id} .edit-btn`).style.display = "none";
        }
    </script>
</body>
</html>
