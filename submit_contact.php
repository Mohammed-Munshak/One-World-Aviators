<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // 1. Insert into Database
    $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
    
    if ($conn->query($sql) === TRUE) {
        $msg_id = $conn->insert_id;

        // 2. Notify Admins
        // We use '0' as actor_id because the sender might be a guest (not logged in)
        // If logged in, you could use $_SESSION['user_id']
        $actor_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; 
        
        $admins = $conn->query("SELECT id FROM users WHERE role='admin'");
        while($admin = $admins->fetch_assoc()) {
            $aid = $admin['id'];
            $conn->query("INSERT INTO notifications (user_id, actor_id, type, reference_id) VALUES ('$aid', '$actor_id', 'contact_msg', '$msg_id')");
        }

        header("Location: contact.php?status=success");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>