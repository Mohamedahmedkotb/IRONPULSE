<?php
include 'db.php'; // Used db.php instead of config.php since that's where $conn is defined

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // encrypts password

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM newusers WHERE email = ?");
    if($check){
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            echo "<h2 style='color:orange;'>⚠️ Email already registered!</h2>";
        } else {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO newusers (name, email, password) VALUES (?, ?, ?)");
            if($stmt){
                $stmt->bind_param("sss", $name, $email, $password);

                if ($stmt->execute()) {
                    echo "<h2 style='color:green;'>✅ Account Created Successfully!</h2>";
                } else {
                    echo "<h2 style='color:red;'>❌ Something went wrong!</h2>";
                    echo $conn->error;
                }
                $stmt->close();
            } else {
                 echo "<h2 style='color:red;'>❌ Database error!</h2>";
                 echo $conn->error;
            }
        }
        $check->close();
    } else {
        echo "<h2 style='color:red;'>❌ Database error!</h2>";
        echo $conn->error;
    }
}
?>
