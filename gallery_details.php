<?php
session_start();
require 'db_connect.php';

// 1. Check if ID exists
if (!isset($_GET['id'])) { header("Location: gallery.php"); exit(); }
$photo_id = mysqli_real_escape_string($conn, $_GET['id']);

// 2. Fetch Photo Details
$sql = "SELECT g.*, u.username, u.profile_pic, u.id as uid 
        FROM plane_spotters_gallery g 
        JOIN users u ON g.user_id = u.id 
        WHERE g.id = '$photo_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) { die("Photo not found."); }
$photo = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $photo['aircraft_model']; ?> - Details</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gd-container { max-width: 900px; margin: 40px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 25px rgba(0,0,0,0.1); }
        
        /* Photo Section */
        .gd-img-box { width: 100%; background: #000; display: flex; justify-content: center; }
        .gd-img { max-width: 100%; max-height: 80vh; object-fit: contain; }
        
        .gd-info { padding: 30px; border-bottom: 1px solid #eee; }
        .gd-title { font-size: 1.8rem; color: var(--primary-color); margin-bottom: 10px; }
        .gd-meta { color: #666; font-size: 0.9rem; margin-bottom: 20px; display: flex; gap: 20px; flex-wrap: wrap; }
        .gd-meta i { color: var(--secondary-color); }
        
        .gd-user { display: flex; align-items: center; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; }
        .gd-avatar { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
        
        /* Comments Section */
        .comments-section { padding: 30px; background: #fafafa; }
        .comment-form { margin-bottom: 30px; display: flex; gap: 10px; }
        .comment-input { flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 5px; }
        
        /* --- COMMENT STYLES --- */
        .comment-box { 
            background: white; 
            padding: 15px; 
            border-radius: 8px; 
            border: 1px solid #eee; 
            margin-bottom: 15px; 
            transition: all 0.3s ease;
        }
        
        .c-header { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; }
        .c-user { font-weight: bold; color: var(--primary-color); text-decoration: none; }
        .c-time { color: #999; font-size: 0.8rem; }
        .c-content { color: #333; line-height: 1.5; }
        
        .reply-btn { background: none; border: none; color: var(--secondary-color); font-size: 0.8rem; cursor: pointer; margin-top: 5px; }
        .reply-btn:hover { text-decoration: underline; }

        .sub-comments { margin-left: 40px; margin-top: 10px; border-left: 2px solid #ddd; padding-left: 15px; }

        /* --- FOOLPROOF HIGHLIGHT CSS --- */
        /* This class is added by JS. !important overrides everything else. */
        .flash-highlight {
            background-color: #fff9c4 !important; /* Bright Yellow */
            border: 2px solid #fbc02d !important; /* Gold Border */
            box-shadow: 0 0 20px rgba(251, 192, 45, 0.6) !important;
            transform: scale(1.02);
            z-index: 10;
            position: relative;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="gd-container">
        <div class="gd-img-box">
            <img src="<?php echo $photo['image_path']; ?>" class="gd-img">
        </div>

        <div class="gd-info">
            <h1 class="gd-title"><?php echo $photo['aircraft_model']; ?></h1>
            <div class="gd-meta">
                <span><i class="fa-solid fa-plane-up"></i> <?php echo $photo['airline_name']; ?></span>
                <span><i class="fa-solid fa-fingerprint"></i> <?php echo $photo['registration_number']; ?></span>
                <?php if($photo['flight_route']): ?>
                    <span><i class="fa-solid fa-route"></i> <?php echo $photo['flight_route']; ?></span>
                <?php endif; ?>
                <span><i class="fa-solid fa-calendar"></i> <?php echo date("M d, Y", strtotime($photo['captured_date'])); ?></span>
            </div>
            <p style="font-size: 1.1rem; font-style: italic; color: #555;">"<?php echo $photo['caption']; ?>"</p>

            <div class="gd-user">
                <img src="<?php echo $photo['profile_pic'] ? $photo['profile_pic'] : 'images/default_avatar.png'; ?>" class="gd-avatar">
                <div>
                    <div>Uploaded by <a href="public_profile.php?id=<?php echo $photo['uid']; ?>" style="text-decoration:none; color:var(--primary-color);"><strong><?php echo $photo['username']; ?></strong></a></div>
                </div>
            </div>
        </div>

        <div class="comments-section">
            <h3><i class="fa-solid fa-comments"></i> Comments</h3>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <form action="submit_comment.php" method="POST" class="comment-form">
                    <input type="hidden" name="photo_id" value="<?php echo $photo_id; ?>">
                    <input type="text" name="content" class="comment-input" placeholder="Add a comment..." required>
                    <button type="submit" class="btn btn-primary">Post</button>
                </form>
            <?php else: ?>
                <p style="margin-bottom:20px;"><a href="login.php">Login</a> to post a comment.</p>
            <?php endif; ?>

            <div id="comments-list">
                <?php
                // Fetch Main Comments (parent_id IS NULL or 0)
                $c_sql = "SELECT c.*, u.username, u.profile_pic, u.id as uid 
                          FROM comments c 
                          JOIN users u ON c.user_id = u.id 
                          WHERE c.gallery_item_id = '$photo_id' AND (c.parent_id IS NULL OR c.parent_id = 0)
                          ORDER BY c.created_at DESC";
                $c_res = $conn->query($c_sql);

                if ($c_res->num_rows > 0) {
                    while($com = $c_res->fetch_assoc()) {
                        $com_id = $com['id'];
                        $pic = !empty($com['profile_pic']) ? $com['profile_pic'] : 'images/default_avatar.png';
                        
                        // --- PARENT COMMENT ID ---
                        echo '<div class="comment-box" id="comment-'.$com_id.'">';

                        echo '  <div class="c-header">
                                    <a href="public_profile.php?id='.$com['uid'].'" class="c-user">
                                        <img src="'.$pic.'" style="width:20px; height:20px; border-radius:50%; vertical-align:middle; margin-right:5px;">
                                        '.$com['username'].'
                                    </a>
                                    <span class="c-time">'.date("M d, g:i a", strtotime($com['created_at'])).'</span>
                                </div>
                                <div class="c-content">'.$com['content'].'</div>
                                
                                <button class="reply-btn" onclick="toggleReply('.$com_id.')"><i class="fa-solid fa-reply"></i> Reply</button>

                                <form action="submit_comment.php" method="POST" id="reply-form-'.$com_id.'" style="display:none; margin-top:10px;">
                                    <input type="hidden" name="photo_id" value="'.$photo_id.'">
                                    <input type="hidden" name="parent_id" value="'.$com_id.'">
                                    <div style="display:flex; gap:5px;">
                                        <input type="text" name="content" class="comment-input" style="padding:8px;" placeholder="Write a reply..." required>
                                        <button type="submit" class="btn btn-secondary" style="padding:8px 15px;">Reply</button>
                                    </div>
                                </form>';

                        // FETCH REPLIES
                        $r_sql = "SELECT c.*, u.username, u.profile_pic, u.id as uid 
                                  FROM comments c 
                                  JOIN users u ON c.user_id = u.id 
                                  WHERE c.parent_id = '$com_id' 
                                  ORDER BY c.created_at ASC";
                        $r_res = $conn->query($r_sql);

                        if($r_res->num_rows > 0){
                            echo '<div class="sub-comments">';
                            while($rep = $r_res->fetch_assoc()){
                                $rep_pic = !empty($rep['profile_pic']) ? $rep['profile_pic'] : 'images/default_avatar.png';
                                
                                // --- REPLY COMMENT ID ---
                                echo '<div class="comment-box" id="comment-'.$rep['id'].'" style="background:#fff; border:none; padding:10px 0;">
                                        <div class="c-header">
                                            <a href="public_profile.php?id='.$rep['uid'].'" class="c-user">
                                                <img src="'.$rep_pic.'" style="width:20px; height:20px; border-radius:50%; vertical-align:middle; margin-right:5px;">
                                                '.$rep['username'].'
                                            </a>
                                            <span class="c-time">'.date("M d, g:i a", strtotime($rep['created_at'])).'</span>
                                        </div>
                                        <div class="c-content">'.$rep['content'].'</div>
                                      </div>';
                            }
                            echo '</div>';
                        }

                        echo '</div>'; // End comment-box
                    }
                } else {
                    echo '<p style="color:#888;">No comments yet. Be the first!</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
    // 1. Toggle Reply Input
    function toggleReply(id) {
        var form = document.getElementById('reply-form-' + id);
        if (form.style.display === 'none') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }

    // 2. HIGHLIGHT LOGIC
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const highlightId = urlParams.get('highlight');

        if (highlightId) {
            console.log("Looking for comment: comment-" + highlightId);
            // Delay to allow layout to settle
            setTimeout(function() {
                const element = document.getElementById("comment-" + highlightId);
                
                if (element) {
                    // Scroll to center
                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Add Flash Class
                    element.classList.add('flash-highlight');
                    
                    // Remove after 3 seconds
                    setTimeout(function() {
                        element.classList.remove('flash-highlight');
                    }, 3000);

                    // Clean URL
                    const newUrl = window.location.href.split('&highlight')[0];
                    window.history.replaceState({}, document.title, newUrl);
                } else {
                    console.error("Comment ID not found.");
                }
            }, 500);
        }
    });
    </script>
</body>
</html>