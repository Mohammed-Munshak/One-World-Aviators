<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = mysqli_real_escape_string($conn, $_POST['username_email']);
    $password = $_POST['password'];

    // Allow login via Username OR Email
    $sql = "SELECT * FROM users WHERE username='$input' OR email='$input'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // 1. Set Session Variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // ---------------------------------------------------------
            // 2. NEW: NOTIFY ADMINS THAT USER LOGGED IN
            // ---------------------------------------------------------
            $actor_id = $user['id'];
            
            // Only notify if the person logging in is NOT an admin (to avoid spamming admins about themselves)
            if($user['role'] != 'admin') {
                $admins = $conn->query("SELECT id FROM users WHERE role='admin'");
                while($admin = $admins->fetch_assoc()) {
                    $aid = $admin['id'];
                    // Insert Notification: Type = 'login'
                    $conn->query("INSERT INTO notifications (user_id, actor_id, type, reference_id) VALUES ('$aid', '$actor_id', 'login', '0')");
                }
            }
            // ---------------------------------------------------------

            // 3. Redirect based on Role
            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            // Password Incorrect
            header("Location: login.php?error=1");
            exit();
        }
    } else {
        // User not found
        header("Location: login.php?error=1");
        exit();
    }
}
?>