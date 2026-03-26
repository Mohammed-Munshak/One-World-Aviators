<?php
session_start();
require 'db_connect.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) { echo json_encode(['status'=>'error']); exit(); }

$uid = $_SESSION['user_id'];
$sid = $_POST['story_id'];

$check = $conn->query("SELECT id FROM story_likes WHERE user_id='$uid' AND story_id='$sid'");

if($check->num_rows > 0) {
    $conn->query("DELETE FROM story_likes WHERE user_id='$uid' AND story_id='$sid'");
    $liked = false;
} else {
    $conn->query("INSERT INTO story_likes (user_id, story_id) VALUES ('$uid', '$sid')");
    $liked = true;
}

$count = $conn->query("SELECT COUNT(*) FROM story_likes WHERE story_id='$sid'")->fetch_row()[0];
echo json_encode(['status'=>'success', 'liked'=>$liked, 'count'=>$count]);
?>