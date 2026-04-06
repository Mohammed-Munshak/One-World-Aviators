<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];

// 1. Fetch Notifications FIRST (Before marking read)
$sql = "SELECT n.*, u.username, u.profile_pic 
        FROM notifications n 
        LEFT JOIN users u ON n.actor_id = u.id 
        WHERE n.user_id = '$user_id' 
        ORDER BY n.created_at DESC LIMIT 50";
$result = $conn->query($sql);

// 2. Mark as Read (Badge clears on NEXT refresh)
$conn->query("UPDATE notifications SET is_read=1 WHERE user_id='$user_id' AND is_read=0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications - OWA</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .notif-container { max-width: 600px; margin: 40px auto; background: white; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #eee; }
        .notif-header { padding: 15px 20px; border-bottom: 1px solid #eee; background: white; font-weight: bold; font-size: 1.1rem; }
        .notif-list { list-style: none; padding: 0; margin: 0; }
        
        .notif-item { 
            padding: 12px 20px; 
            border-bottom: 1px solid #f1f1f1; 
            display: flex; gap: 15px; align-items: center; 
            text-decoration: none; color: #333; 
            transition: background 0.2s; 
            position: relative;
        }
        .notif-item:hover { background: #f9f9f9; }
        
        /* UNREAD DOT STYLE */
        .notif-item.unread { background: #e7f3ff; }
        .notif-item.unread::after {
            content: ''; position: absolute; right: 20px; top: 50%; transform: translateY(-50%);
            width: 10px; height: 10px; background-color: #1877f2; border-radius: 50%;
        }
        
        .avatar-wrapper { position: relative; width: 50px; height: 50px; flex-shrink: 0; }
        .notif-avatar { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
        
        .notif-badge { 
            position: absolute; bottom: -2px; right: -2px; 
            width: 22px; height: 22px; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; 
            color: white; font-size: 0.7rem; border: 2px solid white; 
        }
        
        .notif-content { flex: 1; font-size: 0.9rem; line-height: 1.4; }
        .notif-time { display: block; font-size: 0.75rem; color: #65676b; margin-top: 4px; }

        .bg-blue { background: #1877F2; }
        .bg-green { background: #42b72a; }
        .bg-purple { background: #9c27b0; }
        .bg-orange { background: #f0ad4e; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="content-section">
        <div class="container">
            <div class="notif-container">
                <div class="notif-header">Notifications</div>
                <div class="notif-list">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Check Read Status
                            $status_class = ($row['is_read'] == 0) ? 'unread' : '';
                            $pic = !empty($row['profile_pic']) ? $row['profile_pic'] : 'images/default_avatar.png';
                            $date = date("M j, g:i a", strtotime($row['created_at']));
                            
                            $msg = "New activity."; 
                            $link = "#"; 
                            $icon = '<i class="fa-solid fa-bell"></i>'; 
                            $bg = "bg-blue";

                            // --- SMART LINK LOGIC ---
                            switch ($row['type']) {

                                case 'login':
        $msg = "<strong>{$row['username']}</strong> just logged in.";
        $link = "public_profile.php?id=" . $row['actor_id']; // Click to see who they are
        $icon = '<i class="fa-solid fa-right-to-bracket"></i>'; 
        $bg = "bg-blue";
        break;

        case 'contact_msg':
    $msg = "New contact message from a visitor.";
    $link = "admin_dashboard.php?tab=messages"; // We will create this tab next
    $icon = '<i class="fa-solid fa-envelope"></i>'; $bg = "bg-orange";
    break;
        
                                // 1. UPLOAD (Link to Photo)
                                case 'upload':
                                    $msg = "<strong>{$row['username']}</strong> uploaded a photo.";
                                    $ref_id = $row['reference_id'];
                                    // Fail-Safe: If ID is 0 or deleted, go to gallery main page
                                    if($ref_id > 0) {
                                        $link = "gallery_details.php?id=" . $ref_id;
                                    } else {
                                        $link = "gallery.php"; 
                                    }
                                    $icon = '<i class="fa-solid fa-image"></i>'; $bg = "bg-green";
                                    break;
                                
                                // 2. STORY (Link to College)
                                case 'story':
    $msg = "<strong>{$row['username']}</strong> shared a success story.";
    $sid = $row['reference_id']; // This is Story ID
    
    if($sid > 0) {
        $s_lookup = $conn->query("SELECT college_id FROM college_success_stories WHERE id='$sid'");
        if($s_lookup && $s_r = $s_lookup->fetch_assoc()) {
            // NEW: Added &highlight=SID so it glows
            $link = "college_details.php?id=" . $s_r['college_id'] . "&highlight=" . $sid;
        } else {
            $link = "admin_dashboard.php?tab=stories"; 
        }
    } else {
        $link = "admin_dashboard.php?tab=stories";
    }
    $icon = '<i class="fa-solid fa-trophy"></i>'; $bg = "bg-green";
    break;
                                    
                                // 3. COMMENT (Deep Link)
                                case 'comment':
                                    $msg = "<strong>{$row['username']}</strong> commented on your photo.";
                                    $cid = $row['reference_id'];
                                    if($cid > 0) {
                                        $c_lookup = $conn->query("SELECT gallery_item_id FROM comments WHERE id='$cid'");
                                        if($c_lookup && $c_r = $c_lookup->fetch_assoc()){
                                            $link = "gallery_details.php?id=" . $c_r['gallery_item_id'] . "&highlight=" . $cid;
                                        } else {
                                            $link = "gallery.php";
                                        }
                                    } else {
                                        $link = "gallery.php";
                                    }
                                    $icon = '<i class="fa-solid fa-comment"></i>'; $bg = "bg-purple";
                                    break;

                                // 4. REPLY (Deep Link)
                                case 'reply':
                                    $msg = "<strong>{$row['username']}</strong> replied to your comment.";
                                    $cid = $row['reference_id'];
                                    if($cid > 0) {
                                        $c_lookup = $conn->query("SELECT gallery_item_id FROM comments WHERE id='$cid'");
                                        if($c_lookup && $c_r = $c_lookup->fetch_assoc()){
                                            $link = "gallery_details.php?id=" . $c_r['gallery_item_id'] . "&highlight=" . $cid;
                                        } else {
                                            $link = "gallery.php";
                                        }
                                    } else {
                                        $link = "gallery.php";
                                    }
                                    $icon = '<i class="fa-solid fa-reply"></i>'; $bg = "bg-purple";
                                    break;

                                // 5. ADMIN ACTIONS
                                case 'verification_req':
                                    $msg = "<strong>{$row['username']}</strong> requested verification.";
                                    $link = "admin_dashboard.php?tab=users";
                                    $icon = '<i class="fa-solid fa-shield-halved"></i>'; $bg = "bg-blue";
                                    break;
                                case 'pass_request':
                                    $msg = "<strong>{$row['username']}</strong> requested password reset.";
                                    $link = "admin_dashboard.php?tab=users";
                                    $icon = '<i class="fa-solid fa-key"></i>'; $bg = "bg-orange";
                                    break;

                                case 'forum_post':
    $msg = "<strong>{$row['username']}</strong> asked a new question in The Hangar.";
    // Just link to the top of community page for new posts
    $link = "community.php"; 
    $icon = '<i class="fa-solid fa-comments"></i>'; $bg = "bg-blue";
    break;

case 'forum_comment':
    $msg = "<strong>{$row['username']}</strong> replied to a post.";
    $cid = $row['reference_id'];
    // Need to find which POST this comment belongs to
    $lookup = $conn->query("SELECT post_id FROM forum_comments WHERE id='$cid'");
    if($lookup && $r=$lookup->fetch_assoc()) {
        $link = "community.php?highlight=$cid#post-" . $r['post_id'];
    } else {
        $link = "community.php";
    }
    $icon = '<i class="fa-solid fa-reply"></i>'; $bg = "bg-purple";
    break;
                            }

                            echo '
                            <a href="'.$link.'" class="notif-item '.$status_class.'">
                                <div class="avatar-wrapper">
                                    <img src="'.$pic.'" class="notif-avatar">
                                    <div class="notif-badge '.$bg.'">'.$icon.'</div>
                                </div>
                                <div class="notif-content">
                                    <span class="notif-text">'.$msg.'</span>
                                    <span class="notif-time">'.$date.'</span>
                                </div>
                            </a>';
                        }
                    } else {
                        echo '<div style="padding:40px; text-align:center; color:#888;">No notifications yet.</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
    <?php include 'chatbot.php'; ?>
    <?php include 'footer.php'; ?>
</body>
</html>