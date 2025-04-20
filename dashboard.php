<?php
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

// Check if the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 0) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'dbconnect.php';

// Fetch total appointments
$appointmentQuery = "SELECT COUNT(*) AS total_appointments FROM appointments";
$appointmentResult = mysqli_query($conn, $appointmentQuery);
$totalAppointments = mysqli_fetch_assoc($appointmentResult)['total_appointments'] ?? 0;

// Fetch total registered patients
$petQuery = "SELECT COUNT(*) AS total_pets FROM patients";
$petResult = mysqli_query($conn, $petQuery);
$totalPets = mysqli_fetch_assoc($petResult)['total_pets'] ?? 0;

// Fetch financial data for the graph
$monthlyFinanceQuery = "
    SELECT MONTHNAME(financial_date) AS month, 
           SUM(income) AS total_income, 
           SUM(expense) AS total_expense 
    FROM finances 
    WHERE YEAR(financial_date) = YEAR(CURDATE()) 
    GROUP BY MONTH(financial_date) 
    ORDER BY MONTH(financial_date)";
$monthlyFinanceResult = mysqli_query($conn, $monthlyFinanceQuery);

$months = $incomeData = $expenseData = $profitData = [];
while ($row = mysqli_fetch_assoc($monthlyFinanceResult)) {
    $months[] = $row['month'];
    $incomeData[] = $row['total_income'];
    $expenseData[] = $row['total_expense'];
    $profitData[] = $row['total_income'] - $row['total_expense'];
}

// Fetch available staff (staff on duty)
$staffQuery = "SELECT name FROM staff WHERE status = 'On Duty'";
$staffResult = mysqli_query($conn, $staffQuery);
$staffOnDuty = [];
while ($staffRow = mysqli_fetch_assoc($staffResult)) {
    $staffOnDuty[] = $staffRow['name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PawTracker</title>
    <link rel="stylesheet" href="dashboard.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h2>PawTracker</h2>
            <nav>
            <ul>
                <li class="active"><a href="dashboard.php">Overview</a></li>
                <li><a href="patients.php">Patients</a></li>
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
                <h1>Dashboard</h1>
                <div class="admin-info">
                    <?php echo "Welcome, " . htmlspecialchars($_SESSION['username']); ?>
                </div>
            </header>

            <section class="dashboard-stats">
                <div class="stat-box">
                    <h3>Total Appointments</h3>
                    <p><?php echo $totalAppointments; ?></p>
                </div>
                <div class="stat-box">
                    <h3>Total Registered Pets</h3>
                    <p><?php echo $totalPets; ?></p>
                </div>
            </section>

            <section class="financial-graph">
                <h2>Income & Expenses</h2>
                <button onclick="location.href='finance.php'">Add Financial Data</button>
                <div class="financial-graph-container">
                    <canvas id="financeChart"></canvas>
                </div>
            </section>
        </main>

        <div class="right-panel">
            <div class="appointments-container">
                <h3>Today's Appointments</h3>
                <div class="date-picker">
                    <button onclick="changeDate(-1)">&lt;</button>
                    <span id="selectedDate" data-date="<?php echo date("Y-m-d"); ?>">
                        <?php echo date("l, F j"); ?>
                    </span>
                    <button onclick="changeDate(1)">&gt;</button>
                </div>
                <div class="appointments-list" id="appointmentsList"></div>
            </div>

        </div>
    </div>

    <script>
        function changeDate(offset) {
            let currentDate = new Date(document.getElementById("selectedDate").dataset.date);
            currentDate.setDate(currentDate.getDate() + offset);
            let options = { weekday: "long", month: "long", day: "numeric" };
            let formattedDate = currentDate.toLocaleDateString("en-US", options);
            document.getElementById("selectedDate").innerText = formattedDate;
            document.getElementById("selectedDate").dataset.date = currentDate.toISOString().split("T")[0];
            $.ajax({
                type: "POST",
                url: "fetch_appointments.php",
                data: { date: currentDate.toISOString().split("T")[0] },
                success: function(response) {
                    $("#appointmentsList").html(response);
                }
            });
        }

        $(document).ready(function() {
            changeDate(0);
        });

        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById("financeChart").getContext("2d");
            var financeChart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: <?php echo json_encode($months); ?>,
                    datasets: [
                        { label: "Income", data: <?php echo json_encode($incomeData); ?>, borderColor: "green", backgroundColor: "rgba(0, 255, 0, 0.2)", borderWidth: 2, tension: 0.4 },
                        { label: "Expenses", data: <?php echo json_encode($expenseData); ?>, borderColor: "red", backgroundColor: "rgba(255, 0, 0, 0.2)", borderWidth: 2, tension: 0.4 },
                        { label: "Profit", data: <?php echo json_encode($profitData); ?>, borderColor: "blue", backgroundColor: "rgba(0, 0, 255, 0.2)", borderWidth: 2, tension: 0.4 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });
        });
    </script>
</body>
</html>