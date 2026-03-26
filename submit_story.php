<?php
session_start();
require 'db_connect.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

if($_SERVER['REQUEST_METHOD']=='POST') {
    $uid = $_SESSION['user_id'];
    $cid = $_POST['college_id'];
    $content = mysqli_real_escape_string($conn, $_POST['story']);
    $rating = (int)$_POST['rating'];

    // 1. Insert Story with Rating
    $sql = "INSERT INTO college_success_stories (user_id, college_id, story_content, rating) VALUES ('$uid', '$cid', '$content', '$rating')";
    
    if ($conn->query($sql) === TRUE) {
        $new_story_id = $conn->insert_id;

        // 2. RECALCULATE AVERAGE for this College
        $avg_query = "SELECT AVG(rating) as avg_score FROM college_success_stories WHERE college_id='$cid'";
        $avg_res = $conn->query($avg_query);
        $avg_row = $avg_res->fetch_assoc();
        $new_avg = round($avg_row['avg_score'], 1); // Round to 1 decimal (e.g. 4.5)

        // 3. Update College Table
        $conn->query("UPDATE aviation_colleges SET average_rating='$new_avg' WHERE id='$cid'");

        // 4. Notify Admins
        $admins = $conn->query("SELECT id FROM users WHERE role='admin'");
        while($admin = $admins->fetch_assoc()) {
            $aid = $admin['id'];
            if($aid != $uid) {
                $conn->query("INSERT INTO notifications (user_id, actor_id, type, reference_id) VALUES ('$aid', '$uid', 'story', '$new_story_id')");
            }
        }
    }
    
    header("Location: college_details.php?id=$cid");
}
?>