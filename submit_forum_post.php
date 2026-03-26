<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uid = $_SESSION['user_id'];
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $target_file = NULL;

    // Handle Image Upload
    if (!empty($_FILES["post_image"]["name"])) {
        $target_dir = "images/forum/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $filename = time() . "_" . basename($_FILES["post_image"]["name"]);
        $target_file = $target_dir . $filename;
        move_uploaded_file($_FILES["post_image"]["tmp_name"], $target_file);
    }

    $sql = "INSERT INTO forum_posts (user_id, content, image_path) VALUES ('$uid', '$content', '$target_file')";
    
    if ($conn->query($sql) === TRUE) {
        $post_id = $conn->insert_id;
        
        // Notify Admins
        $admins = $conn->query("SELECT id FROM users WHERE role='admin'");
        while($admin = $admins->fetch_assoc()) {
            if($admin['id'] != $uid) {
                // Type: 'forum_post'
                $conn->query("INSERT INTO notifications (user_id, actor_id, type, reference_id) VALUES ('{$admin['id']}', '$uid', 'forum_post', '$post_id')");
            }
        }
        
        header("Location: community.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>