<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Sanitize Input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 2. Basic Validation
    if ($password !== $confirm_password) {
        die("Error: Passwords do not match. <a href='signup.php'>Try again</a>");
    }

    // 3. Check if Email Already Exists
    $check_sql = "SELECT id FROM users WHERE email = '$email'";
    $result = $conn->query($check_sql);
    if ($result->num_rows > 0) {
        die("Error: Email already registered. <a href='login.php'>Login here</a>");
    }

    // 4. HASH THE PASSWORD (The Security Step)
    // PASSWORD_DEFAULT uses the strongest algorithm available (Bcrypt)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 5. Insert User into Database
    // Note: We save $hashed_password, NOT $password
    $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp']);
    $sql = "INSERT INTO users (username, email, whatsapp_number, password, role) VALUES ('$username', '$email', '$whatsapp', '$hashed_password', 'user')";

    if ($conn->query($sql) === TRUE) {
        // Success! Redirect to login
        header("Location: login.php?msg=registered");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>