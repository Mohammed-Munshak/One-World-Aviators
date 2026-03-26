<?php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Login required']);
    exit();
}

$user_id = $_SESSION['user_id'];
$target_id = $_POST['id'];
$type = $_POST['type']; // 'post' or 'comment'

// Check if already liked
$check = $conn->query("SELECT id FROM forum_likes WHERE user_id='$user_id' AND target_id='$target_id' AND target_type='$type'");

if ($check->num_rows > 0) {
    // UNLIKE
    $conn->query("DELETE FROM forum_likes WHERE user_id='$user_id' AND target_id='$target_id' AND target_type='$type'");
    $action = 'unliked';
} else {
    // LIKE
    $conn->query("INSERT INTO forum_likes (user_id, target_id, target_type) VALUES ('$user_id', '$target_id', '$type')");
    $action = 'liked';
    
    // Notify Owner (Optional: You can add notification logic here later)
}

// Get New Count
$count_res = $conn->query("SELECT COUNT(*) FROM forum_likes WHERE target_id='$target_id' AND target_type='$type'");
$count = $count_res->fetch_row()[0];

echo json_encode(['status' => 'success', 'action' => $action, 'count' => $count]);
?>