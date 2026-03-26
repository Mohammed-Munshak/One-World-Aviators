<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    if (isset($_POST['is_ajax'])) {
        echo json_encode(['status' => 'error', 'message' => 'Please login first']);
        exit();
    }
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- FUNCTION TO SEND NOTIFICATION ---
function sendNotification($conn, $receiver_id, $actor_id, $type, $ref_id) {
    if ($receiver_id == $actor_id) return; // Don't notify yourself

    $check = $conn->query("SELECT id FROM notifications WHERE user_id='$receiver_id' AND actor_id='$actor_id' AND type='$type' AND reference_id='$ref_id' AND is_read=0");
    if($check->num_rows == 0) {
        $sql = "INSERT INTO notifications (user_id, actor_id, type, reference_id) VALUES ('$receiver_id', '$actor_id', '$type', '$ref_id')";
        $conn->query($sql);
    }
}

// =========================================================
// 1. HANDLE LIKES (Gallery Only)
// =========================================================
if (isset($_POST['action']) && $_POST['action'] == 'toggle_like') {
    $gallery_id = $_POST['gallery_id'];

    $check = $conn->query("SELECT id FROM likes WHERE user_id='$user_id' AND gallery_id='$gallery_id'");

    if ($check->num_rows > 0) {
        $conn->query("DELETE FROM likes WHERE user_id='$user_id' AND gallery_id='$gallery_id'");
        $liked = false;
    } else {
        $conn->query("INSERT INTO likes (user_id, gallery_id) VALUES ('$user_id', '$gallery_id')");
        $liked = true;

        $photo = $conn->query("SELECT user_id FROM plane_spotters_gallery WHERE id='$gallery_id'")->fetch_assoc();
        sendNotification($conn, $photo['user_id'], $user_id, 'like', $gallery_id);
    }

    $count = $conn->query("SELECT COUNT(*) FROM likes WHERE gallery_id='$gallery_id'")->fetch_row()[0];
    echo json_encode(['status' => 'success', 'liked' => $liked, 'count' => $count]);
    exit();
}

// =========================================================
// 2. HANDLE COMMENTS (Gallery AND Programs)
// =========================================================
if (isset($_POST['action']) && $_POST['action'] == 'add_comment') {
    
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $parent_id = (isset($_POST['parent_id']) && is_numeric($_POST['parent_id'])) ? $_POST['parent_id'] : "NULL";
    
    // Determine if it is for Gallery or Program
    $gallery_id = isset($_POST['gallery_id']) ? $_POST['gallery_id'] : "NULL";
    $program_id = isset($_POST['program_id']) ? $_POST['program_id'] : "NULL";

    if (!empty($comment)) {
        $sql = "INSERT INTO comments (user_id, gallery_id, program_id, content, parent_comment_id, status) 
                VALUES ('$user_id', $gallery_id, $program_id, '$comment', $parent_id, 'approved')";
        
        if ($conn->query($sql) === TRUE) {
            
            // --- NOTIFICATION LOGIC ---
            if ($parent_id != "NULL") {
                // REPLY: Notify the original commenter
                $parent_query = $conn->query("SELECT user_id FROM comments WHERE id='$parent_id'");
                if ($parent_query->num_rows > 0) {
                    $parent_owner = $parent_query->fetch_assoc();
                    // Ref ID is generally the gallery_id, but for programs we can use program_id
                    $ref_id = ($gallery_id != "NULL") ? $gallery_id : $program_id;
                    sendNotification($conn, $parent_owner['user_id'], $user_id, 'reply', $ref_id);
                }
            } else {
                // MAIN COMMENT: Notify Photo Owner (Only for Gallery)
                if($gallery_id != "NULL") {
                    $photo_query = $conn->query("SELECT user_id FROM plane_spotters_gallery WHERE id='$gallery_id'");
                    if ($photo_query->num_rows > 0) {
                        $photo_owner = $photo_query->fetch_assoc();
                        sendNotification($conn, $photo_owner['user_id'], $user_id, 'comment', $gallery_id);
                    }
                }
            }
        }
    }

    // Redirect Back Correctly
    if($gallery_id != "NULL") {
        header("Location: gallery.php");
    } else {
        header("Location: programs.php");
    }
    exit();
}
?>