<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$uid = $_SESSION['user_id'];

// 1. Check if user has WhatsApp Number
$check = $conn->query("SELECT whatsapp_number, username FROM users WHERE id='$uid'")->fetch_assoc();

if (empty($check['whatsapp_number'])) {
    // If no number, redirect back with error
    header("Location: user_profile.php?msg=missing_whatsapp");
    exit();
}

// 2. Send Notification to ALL Admins
// We use a custom type 'verification_req'
$admins = $conn->query("SELECT id FROM users WHERE role='admin'");
while($admin = $admins->fetch_assoc()) {
    $aid = $admin['id'];
    // Avoid duplicate notifications
    $dup = $conn->query("SELECT id FROM notifications WHERE user_id='$aid' AND actor_id='$uid' AND type='verification_req' AND is_read=0");
    if($dup->num_rows == 0) {
        $conn->query("INSERT INTO notifications (user_id, actor_id, type, reference_id) VALUES ('$aid', '$uid', 'verification_req', '0')");
    }
}

// 3. Redirect back with success
header("Location: user_profile.php?msg=req_sent");
exit();
?>