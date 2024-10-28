<?php
session_start();
$conn = new mysqli("localhost", "root", "root@vivek@1986", "php_jquery_modal_mgmt", '3307');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Fetch the user data
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['username'] = $username; // Set session variable
            // Update login timestamp into DB
            $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE username = ?");
            $stmt->bind_param("s", $username); // Bind the username parameter
            $stmt->execute(); // Execute the query
            echo "success"; // Return success message
        } else {
            echo "Invalid password."; // Invalid password
        }
    } else {
        echo "User does not exist."; // User not found
    }
}

$conn->close();
?>
