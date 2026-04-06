<?php
session_start();
include 'db_connect.php';
$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// --- RECURSIVE FUNCTION TO DISPLAY COMMENTS ---
function display_comments($comments, $parent_id = NULL, $level = 0, $current_user_id, $gid, $parent_username = NULL) {
    foreach ($comments as $comment) {
        if ($comment['parent_comment_id'] == $parent_id) {
            
            $margin_left = ($level < 5) ? 30 : 0; 
            
            echo '<div class="comment-box" style="margin-left: '.$margin_left.'px;">
                    <div class="comment-header">
                        <div>
                            <a href="public_profile.php?id='.$comment['user_id'].'" class="comment-author">'.$comment['username'].'</a>';
                            
                            // SHOW "Replying to X" IF THIS IS A REPLY
                            if ($parent_username) {
                                echo ' <span class="replying-to"><i class="fa-solid fa-share"></i> '.$parent_username.'</span>';
                            }

            echo '      </div>
                        <span class="comment-date">'.date("M j, g:i a", strtotime($comment['created_at'])).'</span>
                    </div>
                    
                    <div class="comment-body">
                        '.$comment['content'].'
                    </div>
                    
                    <div class="comment-actions">';
                        if($current_user_id) {
                            echo '<button class="btn-reply" onclick="toggleReplyForm('.$comment['id'].')">Reply</button>';
                        }
            echo '  </div>';

            if($current_user_id) {
                echo '<form action="submit_interaction.php" method="POST" id="reply-form-'.$comment['id'].'" class="reply-form" style="display:none;">
                        <input type="hidden" name="action" value="add_comment">
                        <input type="hidden" name="gallery_id" value="'.$gid.'">
                        <input type="hidden" name="parent_id" value="'.$comment['id'].'">
                        <input type="text" name="comment" placeholder="Reply to '.$comment['username'].'..." required>
                        <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                      </form>';
            }

            echo '</div>'; // End comment-box

            // RECURSION: Pass CURRENT username as the PARENT for the next level
            display_comments($comments, $comment['id'], $level + 1, $current_user_id, $gid, $comment['username']);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gallery - One World Aviators</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gallery-item { margin-bottom: 40px; border: 1px solid #eee; border-radius: 10px; overflow: hidden; background: white; }
        .gallery-details { padding: 20px; }
        
        /* Photo Details Badge Style */
        .photo-info-badges { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .p-badge { background: #f4f6f8; color: #555; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: 500; }
        .p-badge i { color: var(--secondary-color); margin-right: 5px; }

        .like-btn { border: none; background: none; cursor: pointer; font-size: 1.2rem; transition: 0.2s; }
        .like-btn.liked { color: #e74c3c; }
        
        .comment-section { background: #fafafa; padding: 15px; border-top: 1px solid #eee; }
        .comment-box { position: relative; background: #fff; border: 1px solid #e1e1e1; border-radius: 8px; padding: 10px 15px; margin-bottom: 10px; border-left: 3px solid var(--secondary-color); }
        .comment-header { display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: #777; margin-bottom: 5px; }
        .comment-author { font-weight: bold; color: var(--primary-color); font-size: 0.95rem; text-decoration: none; }
        .comment-author:hover { text-decoration: underline; }
        
        .replying-to { background-color: #f0f2f5; color: #65676b; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-left: 8px; font-weight: 600; }
        .btn-reply { background: none; border: none; color: var(--secondary-color); font-size: 0.8rem; cursor: pointer; margin-top: 5px; padding: 0; font-weight: 600; }
        .reply-form { margin-top: 10px; display: flex; gap: 5px; }
        .reply-form input { flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.9rem; }
        .reply-form button { background: var(--primary-color); color: white; border: none; padding: 0 15px; border-radius: 4px; cursor: pointer; }
        
        .main-comment-form { display: flex; gap: 10px; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 20px; }
        .main-comment-form input { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 20px; }
        .main-comment-form button { background: var(--primary-color); color: white; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="page-header">
        <div class="container">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h1>Plane Spotters Gallery</h1>
                    <p>Through the lens of Sri Lanka's aviation enthusiasts</p>
                </div>
                <?php if($current_user_id): ?>
                    <a href="upload_photo.php" class="btn btn-primary"><i class="fa-solid fa-cloud-arrow-up"></i> Upload Photo</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-secondary">Login to Upload</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="gallery-grid">
                <?php
                $sql = "SELECT g.*, u.username FROM plane_spotters_gallery g 
                        JOIN users u ON g.user_id = u.id 
                        WHERE g.status = 'approved' 
                        ORDER BY g.upload_date DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $gid = $row['id'];
                        
                        $like_count = $conn->query("SELECT COUNT(*) FROM gallery_likes WHERE gallery_id='$gid'")->fetch_row()[0];
                        $user_liked = false;
                        if($current_user_id) {
                            $check_like = $conn->query("SELECT id FROM gallery_likes WHERE user_id='$current_user_id' AND gallery_id='$gid'");
                            if($check_like->num_rows > 0) $user_liked = true;
                        }
                        $heart_class = $user_liked ? 'fa-solid fa-heart' : 'fa-regular fa-heart';
                        $btn_class = $user_liked ? 'liked' : '';

                        echo '
                        <div class="gallery-item">
                            <div class="gallery-img">
                                <img src="'.$row['image_path'].'" alt="'.$row['aircraft_model'].'">
                            </div>
                            <div class="gallery-overlay">
                                <span class="model">'.$row['aircraft_model'].'</span>
                                <span class="photographer">By: '.$row['username'].'</span>
                            </div>
                            <div class="gallery-details">
                                <p class="caption">"'.$row['caption'].'"</p>

                                <div class="photo-info-badges">
                                    <span class="p-badge"><i class="fa-solid fa-plane-up"></i> '.$row['airline_name'].'</span>
                                    <span class="p-badge"><i class="fa-solid fa-fingerprint"></i> Reg: '.$row['registration_number'].'</span>';
                                    if(!empty($row['flight_route'])) {
                                        echo '<span class="p-badge"><i class="fa-solid fa-route"></i> '.$row['flight_route'].'</span>';
                                    }
                        echo '  </div>
                                
                                <div class="gallery-meta">
                                    <span><i class="fa-regular fa-calendar"></i> '.date("M j", strtotime($row['captured_date'])).'</span>
                                    <button class="like-btn '.$btn_class.'" onclick="toggleLike('.$gid.')" id="like-btn-'.$gid.'">
                                        <i class="'.$heart_class.'"></i> <span id="like-count-'.$gid.'">'.$like_count.'</span>
                                    </button>
                                </div>
                            </div>

                            <div class="comment-section">';
                                $comments_array = [];
                                $comm_sql = "SELECT c.*, u.username FROM comments c 
                                             JOIN users u ON c.user_id = u.id 
                                             WHERE gallery_id='$gid' ORDER BY created_at ASC";
                                $comm_res = $conn->query($comm_sql);
                                while($c = $comm_res->fetch_assoc()) { $comments_array[] = $c; }

                                if (!empty($comments_array)) {
                                    display_comments($comments_array, NULL, 0, $current_user_id, $gid, NULL);
                                } else {
                                    echo '<div style="text-align:center; color:#999; font-style:italic; padding:10px;">Be the first to comment!</div>';
                                }

                                if ($current_user_id) {
                                    echo '
                                    <form action="submit_interaction.php" method="POST" class="main-comment-form">
                                        <input type="hidden" name="action" value="add_comment">
                                        <input type="hidden" name="gallery_id" value="'.$gid.'">
                                        <input type="text" name="comment" placeholder="Write a comment..." required>
                                        <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                                    </form>';
                                } else {
                                    echo '<p style="text-align:center; margin-top:10px; font-size:0.9rem;"><a href="login.php" style="color:var(--secondary-color);">Login</a> to join the conversation.</p>';
                                }
                        echo '</div>
                        </div>';
                    }
                } else {
                    echo '<div class="alert-box"><h3>No photos uploaded yet.</h3></div>';
                }
                ?>
            </div>
        </div>
    </section>
    <?php include 'footer.php'; ?>
    <script>
    function toggleLike(galleryId) {
        const formData = new FormData();
        formData.append('action', 'toggle_like');
        formData.append('gallery_id', galleryId);
        formData.append('is_ajax', '1');
        fetch('submit_interaction.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                document.getElementById('like-count-' + galleryId).innerText = data.count;
                const btn = document.getElementById('like-btn-' + galleryId);
                const icon = btn.querySelector('i');
                if(data.liked) {
                    btn.classList.add('liked'); icon.classList.remove('fa-regular'); icon.classList.add('fa-solid');
                } else {
                    btn.classList.remove('liked'); icon.classList.remove('fa-solid'); icon.classList.add('fa-regular');
                }
            } else if (data.message) { alert(data.message); }
        });
    }
    function toggleReplyForm(commentId) {
        var form = document.getElementById('reply-form-' + commentId);
        form.style.display = (form.style.display === "none") ? "flex" : "none";
        if(form.style.display === "flex") form.querySelector('input').focus();
    }
    </script>
    <?php include 'chatbot.php'; ?>
</body>
</html>