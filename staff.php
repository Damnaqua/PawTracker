<?php
session_start();
include 'dbconnect.php';

// Fetch staff (vets) data
$query = "SELECT vet_id, vet_name, specialization, on_duty FROM vets ORDER BY vet_name ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("SQL Error: " . mysqli_error($conn)); // Debug query error
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>
    <link rel="stylesheet" href="staff.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo"></div>
            <h2>PawTracker</h2>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Overview</a></li>
                    <li><a href="patients.php">Patients</a></li>
                    <li class="active"><a href="staff.php">Staff</a></li>
                    <li><a href="status.php">Status</a></li>
                    <li><a href="calendar.php">Calendar</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="announce.php">Announcement</a></li>
                </ul>
            </nav>
        </aside>

        <main class="content">
            <div class="header">
                <input type="text" placeholder="Search for staff">
                <a href="add_staff.php" class="add-staff">+ Add new staff</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Staff’s name</th>
                        <th>Role</th>
                        <th>Availabality</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                        <td>Dr. <?php echo htmlspecialchars($row['vet_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['specialization']); ?></td>
                            <td class="<?php echo strtolower(str_replace(' ', '-', $row['on_duty'])); ?>">
                                <?php echo htmlspecialchars($row['on_duty']) ?: 'Not Available'; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="pagination">
                <span>< 1 2 3 4 5 6 ></span>
            </div>
        </main>
    </div>
</body>
</html>
