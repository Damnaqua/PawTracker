<?php
// Sambungkan ke database
$servername = "localhost"; // Tukar ikut server kau
$username = "root"; // Tukar ikut username MySQL
$password = ""; // Tukar ikut password MySQL
$dbname = "alynnvet"; // Tukar dengan nama database sebenar

$conn = new mysqli($servername, $username, $password, $dbname);

// Semak connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Semak jika form dihantar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $income = isset($_POST['income']) ? $_POST['income'] : 0;
    $expense = isset($_POST['expense']) ? $_POST['expense'] : 0;
    $financial_date = $_POST['financial_date'];

    // Elakkan SQL Injection
    $income = $conn->real_escape_string($income);
    $expense = $conn->real_escape_string($expense);
    $financial_date = $conn->real_escape_string($financial_date);

    // Masukkan data ke dalam database
    $sql = "INSERT INTO finances (income, expense, financial_date) VALUES ('$income', '$expense', '$financial_date')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Record added successfully!'); window.location.href='finance.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Financial Data</title>
    <link rel="stylesheet" href="finance.css">
</head>
<body>

    <div class="container">
        <h2>Add Financial Record</h2>

        <form method="POST" action="">
            <div class="form-group">
                <label>Income:</label>
                <input type="number" step="0.01" name="income" required>
            </div>

            <div class="form-group">
                <label>Expense:</label>
                <input type="number" step="0.01" name="expense" required>
            </div>

            <div class="form-group">
                <label>Date:</label>
                <input type="date" name="financial_date" required>
            </div>

            <button type="submit">Add Record</button>
        </form>

        <a href="dashboard.php" class="back-link">Back</a>
    </div>

</body>
</html>
