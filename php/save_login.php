<?php
include 'config.php';

// Get data from login form
$email    = $_POST['email'];
$password = $_POST['password'];

// Save into currentuser table
$sql  = "INSERT INTO currentuser (email, password) VALUES (?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $email, $password);

if (mysqli_stmt_execute($stmt)) {
    echo "<h2 style='color:green;'>✅ Data Saved Successfully!</h2>";
} else {
    echo "<h2 style='color:red;'>❌ Failed to Save Data</h2>";
    echo mysqli_error($conn);
}
?>