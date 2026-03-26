<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: community.php"); exit();
}

$uid = $_SESSION['user_id'];
$id = mysqli_real_escape_string($conn, $_GET['id']);
$type = $_GET['type'];
$table = ($type == 'post') ? 'forum_posts' : 'forum_comments';

// Check Ownership or Admin Role
$check = $conn->query("SELECT user_id FROM $table WHERE id='$id'")->fetch_assoc();

if ($check && ($check['user_id'] == $uid || $_SESSION['role'] == 'admin')) {
    $conn->query("DELETE FROM $table WHERE id='$id'");
}

header("Location: community.php");
?>