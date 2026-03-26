<?php
function notifyAdmins($conn, $actor_id, $type, $message) {
    // 1. Find all admins
    $admins = $conn->query("SELECT id FROM users WHERE role='admin'");
    while($admin = $admins->fetch_assoc()) {
        $admin_id = $admin['id'];
        // Don't notify if the admin did the action themselves
        if($admin_id != $actor_id) {
            $sql = "INSERT INTO notifications (user_id, actor_id, type, reference_id) VALUES ('$admin_id', '$actor_id', '$type', '0')"; 
            // Note: We use '0' for reference_id for general alerts, or you can add a 'message' column to your notifications table
            // For now, let's assume we update the table to store a custom message or adapt the type.
            
            // To make this work with your existing table, we will cheat slightly and use the 'type' to store the message if it's not a standard type, 
            // OR better: Update the table. Let's stick to your existing structure but add a specific type.
            
            // Let's strictly use existing logic:
            $conn->query("INSERT INTO notifications (user_id, actor_id, type, reference_id) VALUES ('$admin_id', '$actor_id', 'general', '0')");
        }
    }
}
?>