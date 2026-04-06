<?php
session_start();
require 'db_connect.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    $is_ajax = isset($_POST['is_ajax']) && $_POST['is_ajax'] == '1';
    if ($is_ajax) {
        echo json_encode(['status' => 'error', 'message' => 'You must be logged in.']);
    } else {
        header("Location: login.php");
    }
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$action  = isset($_POST['action']) ? $_POST['action'] : '';

// ============================================================
// ACTION: TOGGLE LIKE
// Uses table: gallery_likes (columns: id, user_id, gallery_id)
// ============================================================
if ($action === 'toggle_like') {
    $gallery_id = isset($_POST['gallery_id']) ? (int) $_POST['gallery_id'] : 0;
    $is_ajax    = isset($_POST['is_ajax']) && $_POST['is_ajax'] == '1';

    if ($gallery_id <= 0) {
        if ($is_ajax) echo json_encode(['status' => 'error', 'message' => 'Invalid gallery ID.']);
        exit();
    }

    // Check if already liked
    $check = $conn->query("SELECT id FROM gallery_likes WHERE user_id='$user_id' AND gallery_id='$gallery_id'");

    if ($check->num_rows > 0) {
        // Already liked — remove the like
        $conn->query("DELETE FROM gallery_likes WHERE user_id='$user_id' AND gallery_id='$gallery_id'");
        $liked = false;
    } else {
        // Not liked yet — add like
        $conn->query("INSERT INTO gallery_likes (user_id, gallery_id) VALUES ('$user_id', '$gallery_id')");
        $liked = true;
    }

    // Get updated count
    $count = $conn->query("SELECT COUNT(*) FROM gallery_likes WHERE gallery_id='$gallery_id'")->fetch_row()[0];

    if ($is_ajax) {
        echo json_encode([
            'status' => 'success',
            'liked'  => $liked,
            'count'  => $count
        ]);
    } else {
        header("Location: gallery.php");
    }
    exit();
}

// ============================================================
// ACTION: ADD COMMENT
// Uses table: comments
// Columns used: user_id, content, gallery_id, parent_comment_id
// ============================================================
if ($action === 'add_comment') {
    $gallery_id = isset($_POST['gallery_id']) ? (int) $_POST['gallery_id'] : 0;
    $comment    = isset($_POST['comment'])    ? trim($_POST['comment'])    : '';
    $parent_id  = isset($_POST['parent_id'])  ? (int) $_POST['parent_id']  : NULL;

    if ($gallery_id <= 0 || $comment === '') {
        header("Location: gallery.php");
        exit();
    }

    $comment = mysqli_real_escape_string($conn, $comment);

    if ($parent_id) {
        // Reply — store in parent_comment_id
        $conn->query("INSERT INTO comments (user_id, content, gallery_id, parent_comment_id) 
                      VALUES ('$user_id', '$comment', '$gallery_id', '$parent_id')");
    } else {
        // Top-level comment — parent_comment_id is NULL
        $conn->query("INSERT INTO comments (user_id, content, gallery_id, parent_comment_id) 
                      VALUES ('$user_id', '$comment', '$gallery_id', NULL)");
    }

    header("Location: gallery.php#gallery-item-$gallery_id");
    exit();
}

// Fallback
header("Location: gallery.php");
exit();
?>