<?php
$host     = "localhost";
$user     = "root";
$password = "";
$database = "IRONDATA";  // 👈 Change this to your database name

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    echo "<h2 style='color:red;'>❌ Connection Failed!</h2>";
    echo "<p>" . mysqli_connect_error() . "</p>";
} else {
    echo "<h2 style='color:green;'>✅ Connection Successful!</h2>";
    echo "<p>Connected to database: <b>" . $database . "</b></p>";
}
?>