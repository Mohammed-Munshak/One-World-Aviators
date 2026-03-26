<?php
session_start();
require 'db_connect.php';

// Ensure we always return JSON, even if there is an error
header('Content-Type: application/json');

// Disable default PHP error printing (it breaks JSON)
ini_set('display_errors', 0);
error_reporting(0);

$response = [];

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Please login to react.');
    }

    $user_id = $_SESSION['user_id'];
    $news_id = isset($_POST['news_id']) ? (int)$_POST['news_id'] : 0;
    $type = isset($_POST['type']) ? $_POST['type'] : '';

    if ($news_id <= 0 || empty($type)) {
        throw new Exception("Invalid data. ID: $news_id");
    }

    // --- DATABASE OPERATIONS (Using SINGULAR table 'news_reaction') ---

    // 1. Check existing
    $check = $conn->query("SELECT reaction_type FROM news_reactions WHERE news_id='$news_id' AND user_id='$user_id'");
    
    // Check for SQL errors
    if (!$check) {
        throw new Exception("Database Error 1: " . $conn->error);
    }

    $action = '';

    if ($check->num_rows > 0) {
        $row = $check->fetch_assoc();
        if ($row['reaction_type'] == $type) {
            // Remove
            $conn->query("DELETE FROM news_reactions WHERE news_id='$news_id' AND user_id='$user_id'");
            $action = 'removed';
        } else {
            // Update
            $conn->query("UPDATE news_reactions SET reaction_type='$type' WHERE news_id='$news_id' AND user_id='$user_id'");
            $action = 'updated';
        }
    } else {
        // Add
        $insert = $conn->query("INSERT INTO news_reactions (news_id, user_id, reaction_type) VALUES ('$news_id', '$user_id', '$type')");
        if (!$insert) {
            throw new Exception("Database Error 2: " . $conn->error);
        }
        $action = 'added';
    }

    // 2. Get Counts
    $counts = ['like'=>0, 'love'=>0, 'wow'=>0, 'sad'=>0, 'angry'=>0];
    $res = $conn->query("SELECT reaction_type, COUNT(*) as c FROM news_reactions WHERE news_id='$news_id' GROUP BY reaction_type");
    
    if ($res) {
        while($row = $res->fetch_assoc()){
            $counts[$row['reaction_type']] = $row['c'];
        }
    }

    echo json_encode(['status' => 'success', 'action' => $action, 'counts' => $counts]);

} catch (Exception $e) {
    // Return the specific error message to the popup
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>