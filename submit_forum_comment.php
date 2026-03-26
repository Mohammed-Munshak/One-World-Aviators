<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uid = $_SESSION['user_id'];
    $pid = $_POST['post_id'];
    $content = mysqli_real_escape_string($conn, $_POST['comment']);
    $target_file = NULL;

    // Handle Image Upload for Comment
    if (!empty($_FILES["comment_image"]["name"])) {
        $target_dir = "images/forum/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $filename = "c_" . time() . "_" . basename($_FILES["comment_image"]["name"]);
        $target_file = $target_dir . $filename;
        move_uploaded_file($_FILES["comment_image"]["tmp_name"], $target_file);
    }

    $sql = "INSERT INTO forum_comments (post_id, user_id, content, image_path) VALUES ('$pid', '$uid', '$content', '$target_file')";
    
    if ($conn->query($sql) === TRUE) {
        $cid = $conn->insert_id;

        // 1. Notify Post Owner
        $post_res = $conn->query("SELECT user_id FROM forum_posts WHERE id='$pid'");
        $post_owner = $post_res->fetch_assoc()['user_id'];
        
        if ($post_owner != $uid) {
            $conn->query("INSERT INTO notifications (user_id, actor_id, type, reference_id) VALUES ('$post_owner', '$uid', 'forum_comment', '$cid')");
        }

        // 2. Notify Admins
        $admins = $conn->query("SELECT id FROM users WHERE role='admin'");
        while($admin = $admins->fetch_assoc()) {
            if($admin['id'] != $uid && $admin['id'] != $post_owner) {
                $conn->query("INSERT INTO notifications (user_id, actor_id, type, reference_id) VALUES ('{$admin['id']}', '$uid', 'forum_comment', '$cid')");
            }
        }

        // Redirect back to specific post
        header("Location: community.php?#post-$pid");
    }
}
?>