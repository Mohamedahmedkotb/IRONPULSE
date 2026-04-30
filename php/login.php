<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, password FROM newusers WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                
                // Allow both hashed and plaintext for testing purposes
                if (password_verify($password, $user['password']) || $password === $user['password']) {
                    // Login successful
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['email'] = $email;
                    
                    header("Location: ../html/Userprofile.html");
                    exit();
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Invalid email or password.";
            }
            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    } else {
        $error = "Please enter both email and password.";
    }
} else {
    // If accessed via GET, redirect to login page
    header("Location: ../html/login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Error</title>
</head>
<body>
    <script>
        alert("<?php echo addslashes($error); ?>");
        window.location.href = "../html/login.html";
    </script>
</body>
</html>
