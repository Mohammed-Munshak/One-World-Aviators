<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) { exit("Not logged in"); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $photo_id = mysqli_real_escape_string($conn, $_POST['photo_id']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $parent_id = isset($_POST['parent_id']) && !empty($_POST['parent_id']) ? $_POST['parent_id'] : 'NULL';

    $sql = "INSERT INTO comments (user_id, gallery_item_id, parent_id, content) VALUES ('$user_id', '$photo_id', $parent_id, '$content')";
    
    if ($conn->query($sql) === TRUE) {
        $new_comment_id = $conn->insert_id;

        // 1. Notify Photo Owner (ONLY if it's not me)
        $photo_res = $conn->query("SELECT user_id FROM plane_spotters_gallery WHERE id='$photo_id'");
        $photo_owner = ($photo_res->num_rows > 0) ? $photo_res->fetch_assoc()['user_id'] : 0;

        if ($photo_owner != 0 && $photo_owner != $user_id) {
            $conn->query("INSERT INTO notifications (user_id, actor_id, type, reference_id) VALUES ('$photo_owner', '$user_id', 'comment', '$new_comment_id')");
        }

        // 2. Notify Parent Commenter (ONLY if it's a reply & not me)
        if ($parent_id != 'NULL') {
            $parent_res = $conn->query("SELECT user_id FROM comments WHERE id='$parent_id'");
            if ($parent_res->num_rows > 0) {
                $parent_author = $parent_res->fetch_assoc()['user_id'];
                if ($parent_author != $user_id && $parent_author != $photo_owner) {
                    $conn->query("INSERT INTO notifications (user_id, actor_id, type, reference_id) VALUES ('$parent_author', '$user_id', 'reply', '$new_comment_id')");
                }
            }
        }

        // 3. Notify Admins (ONLY if Admin is not me, and Admin is not owner)
        $admins = $conn->query("SELECT id FROM users WHERE role='admin'");
        while($admin = $admins->fetch_assoc()) {
            $aid = $admin['id'];
            // Stop Madness: Don't notify if Admin is the one commenting
            if ($aid != $user_id && $aid != $photo_owner) {
                $conn->query("INSERT INTO notifications (user_id, actor_id, type, reference_id) VALUES ('$aid', '$user_id', 'comment', '$new_comment_id')");
            }
        }

        header("Location: gallery_details.php?id=$photo_id#comment-$new_comment_id");
        exit();
    }
}
?>