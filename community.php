<?php
session_start();
require 'db_connect.php';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>The Hangar - Community Forum</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f0f2f5; }
        .forum-container { max-width: 700px; margin: 30px auto; }
        
        /* POST BOX */
        .create-post { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .cp-header { font-weight: bold; color: #555; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .cp-textarea { width: 100%; border: none; font-size: 1.1rem; resize: none; outline: none; margin-bottom: 10px; font-family: inherit; }
        .cp-actions { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eee; padding-top: 10px; }
        .file-label { cursor: pointer; color: var(--secondary-color); font-weight: bold; font-size: 0.9rem; }
        
        /* FEED POSTS */
        .post-card { background: white; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; overflow: hidden; position: relative; }
        .post-header { padding: 15px; display: flex; align-items: center; gap: 10px; }
        .post-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        .post-meta { line-height: 1.2; flex-grow: 1; }
        .post-author { font-weight: bold; color: var(--primary-color); text-decoration: none; }
        .post-time { font-size: 0.8rem; color: #888; }
        
        .post-options { position: relative; }
        .options-btn { background: none; border: none; cursor: pointer; color: #666; font-size: 1.1rem; }
        .dropdown-menu { display: none; position: absolute; right: 0; top: 20px; background: white; border: 1px solid #ddd; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 100; min-width: 120px; }
        .dropdown-menu a { display: block; padding: 8px 15px; text-decoration: none; color: #333; font-size: 0.9rem; }
        .dropdown-menu a:hover { background: #f5f5f5; }
        .post-options:hover .dropdown-menu { display: block; }

        .post-content { padding: 0 15px 15px 15px; font-size: 1rem; color: #1c1e21; line-height: 1.5; }
        .post-image { width: 100%; display: block; max-height: 500px; object-fit: cover; border-top: 1px solid #eee; border-bottom: 1px solid #eee; }
        
        /* ACTIONS BAR (Like/Comment) */
        .post-actions { display: flex; border-top: 1px solid #eee; border-bottom: 1px solid #eee; padding: 5px 0; }
        .action-btn { flex: 1; background: none; border: none; padding: 10px; cursor: pointer; color: #666; font-weight: bold; font-size: 0.9rem; transition: 0.2s; }
        .action-btn:hover { background: #f2f2f2; }
        .action-btn.liked { color: #e74c3c; }
        
        /* COMMENT SECTION */
        .comment-section { background: #fafafa; padding: 15px; }
        .comment-list { margin-bottom: 15px; }
        .comment-item { display: flex; gap: 10px; margin-bottom: 15px; }
        .c-avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }
        .c-bubble { background: #f0f2f5; padding: 10px 15px; border-radius: 18px; font-size: 0.9rem; position: relative; max-width: 90%; }
        .c-author { font-weight: bold; font-size: 0.85rem; display: block; color: #333; margin-bottom: 2px; }
        .c-img { max-width: 150px; border-radius: 10px; margin-top: 5px; display: block; cursor: pointer; }
        
        /* PREVIEW AREA */
        .img-preview-box { display: none; margin-top: 10px; position: relative; display: inline-block; }
        .preview-img { max-height: 100px; border-radius: 5px; border: 1px solid #ddd; }
        
        /* COMMENT INPUT */
        .comment-form { display: flex; gap: 10px; align-items: flex-start; margin-top: 10px; }
        .c-input-box { flex: 1; position: relative; background: white; border: 1px solid #ccc; border-radius: 20px; padding: 5px 15px; }
        .c-input { width: 100%; border: none; outline: none; padding: 5px 0; }
        .c-file-label { cursor: pointer; color: #666; font-size: 1.1rem; }
        
        /* Comment Like Heart */
        .c-like-btn { font-size: 0.75rem; color: #666; cursor: pointer; margin-left: 5px; text-decoration: none; }
        .c-like-btn.liked { color: #e74c3c; }
        
        /* Highlight Animation */
        .flash-highlight { background-color: #fff9c4 !important; transition: background 1s ease; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container forum-container">
        
        <?php if($user_id): ?>
        <div class="create-post">
            <div class="cp-header">Create a Post</div>
            <form action="submit_forum_post.php" method="POST" enctype="multipart/form-data">
                <textarea name="content" rows="3" class="cp-textarea" placeholder="What's on your mind, aviator?" required></textarea>
                
                <div id="post-preview-area" style="display:none; margin-bottom:10px;">
                    <img id="post-preview-img" class="preview-img">
                </div>

                <div class="cp-actions">
                    <label class="file-label">
                        <i class="fa-solid fa-image"></i> Add Photo
                        <input type="file" name="post_image" style="display:none;" onchange="previewFile(this, 'post-preview-img', 'post-preview-area')">
                    </label>
                    <button type="submit" class="btn btn-primary">Post</button>
                </div>
            </form>
        </div>
        <?php else: ?>
            <div class="create-post" style="text-align:center;">
                <p><a href="login.php">Login</a> to join the discussion.</p>
            </div>
        <?php endif; ?>

        <?php
        $sql = "SELECT p.*, u.username, u.profile_pic, u.id as uid 
                FROM forum_posts p 
                JOIN users u ON p.user_id = u.id 
                ORDER BY p.created_at DESC";
        $posts = $conn->query($sql);

        if($posts->num_rows > 0) {
            while($post = $posts->fetch_assoc()) {
                $pid = $post['id'];
                $ppic = !empty($post['profile_pic']) ? $post['profile_pic'] : 'images/default_avatar.png';
                $pimg = !empty($post['image_path']) ? '<img src="'.$post['image_path'].'" class="post-image">' : '';
                
                // Post Likes
                $plikes = $conn->query("SELECT COUNT(*) FROM forum_likes WHERE target_id='$pid' AND target_type='post'")->fetch_row()[0];
                $i_liked_post = ($user_id) ? $conn->query("SELECT id FROM forum_likes WHERE user_id='$user_id' AND target_id='$pid' AND target_type='post'")->num_rows > 0 : false;
                $p_cls = $i_liked_post ? 'liked' : '';
                $p_icon = $i_liked_post ? 'fa-solid' : 'fa-regular';

                echo '
                <div class="post-card" id="post-'.$pid.'">
                    <div class="post-header">
                        <img src="'.$ppic.'" class="post-avatar">
                        <div class="post-meta">
                            <a href="public_profile.php?id='.$post['uid'].'" class="post-author">'.$post['username'].'</a>
                            <div class="post-time">'.date("M d, Y", strtotime($post['created_at'])).'</div>
                        </div>';
                        
                        // EDIT/DELETE DROPDOWN
                        if($user_id == $post['user_id'] || $user_role == 'admin') {
                            echo '
                            <div class="post-options">
                                <button class="options-btn"><i class="fa-solid fa-ellipsis"></i></button>
                                <div class="dropdown-menu">
                                    <a href="edit_forum.php?type=post&id='.$pid.'"><i class="fa-solid fa-pen"></i> Edit</a>
                                    <a href="delete_forum.php?type=post&id='.$pid.'" onclick="return confirm(\'Delete this post?\')"><i class="fa-solid fa-trash"></i> Delete</a>
                                </div>
                            </div>';
                        }

                echo '</div>
                    <div class="post-content">'.nl2br($post['content']).'</div>
                    '.$pimg.'
                    
                    <div class="post-actions">
                        <button class="action-btn '.$p_cls.'" onclick="toggleLike(this, \'post\', '.$pid.')">
                            <i class="'.$p_icon.' fa-heart"></i> <span>'.$plikes.'</span> Likes
                        </button>
                        <button class="action-btn" onclick="focusComment('.$pid.')">
                            <i class="fa-regular fa-comment"></i> Comment
                        </button>
                    </div>

                    <div class="comment-section">
                        <div class="comment-list" id="clist-'.$pid.'">';
                        
                        // FETCH COMMENTS
                        $c_sql = "SELECT c.*, u.username, u.profile_pic, u.id as cuid 
                                  FROM forum_comments c 
                                  JOIN users u ON c.user_id = u.id 
                                  WHERE c.post_id = '$pid' ORDER BY c.created_at ASC";
                        $comments = $conn->query($c_sql);
                        
                        while($com = $comments->fetch_assoc()) {
                            $cid = $com['id'];
                            $cpic = !empty($com['profile_pic']) ? $com['profile_pic'] : 'images/default_avatar.png';
                            $cimg = !empty($com['image_path']) ? '<img src="'.$com['image_path'].'" class="c-img">' : '';
                            
                            // Comment Likes
                            $clikes = $conn->query("SELECT COUNT(*) FROM forum_likes WHERE target_id='$cid' AND target_type='comment'")->fetch_row()[0];
                            $i_liked_com = ($user_id) ? $conn->query("SELECT id FROM forum_likes WHERE user_id='$user_id' AND target_id='$cid' AND target_type='comment'")->num_rows > 0 : false;
                            $c_cls = $i_liked_com ? 'liked' : '';

                            echo '
                            <div class="comment-item" id="f-comment-'.$cid.'">
                                <img src="'.$cpic.'" class="c-avatar">
                                <div>
                                    <div class="c-bubble">
                                        <a href="public_profile.php?id='.$com['cuid'].'" class="c-author">'.$com['username'].'</a>
                                        '.$com['content'].'
                                        '.$cimg.'
                                    </div>
                                    <div style="font-size:0.75rem; margin-top:2px; margin-left:10px; color:#666;">
                                        <span class="c-like-btn '.$c_cls.'" onclick="toggleLike(this, \'comment\', '.$cid.')">
                                            <i class="fa-solid fa-heart"></i> <span>'.$clikes.'</span>
                                        </span>';
                                        
                                        // Edit/Delete for Comment
                                        if($user_id == $com['user_id'] || $user_role == 'admin') {
                                            echo ' &bull; <a href="edit_forum.php?type=comment&id='.$cid.'" style="color:#666; text-decoration:none;">Edit</a>
                                                   &bull; <a href="delete_forum.php?type=comment&id='.$cid.'" onclick="return confirm(\'Delete?\')" style="color:#666; text-decoration:none;">Delete</a>';
                                        }
                            echo '  </div>
                                </div>
                            </div>';
                        }

                echo '  </div>';

                if($user_id) {
                    echo '
                    <form action="submit_forum_comment.php" method="POST" enctype="multipart/form-data" class="comment-form">
                        <img src="'.(!empty($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'images/default_avatar.png').'" class="c-avatar">
                        <input type="hidden" name="post_id" value="'.$pid.'">
                        
                        <div style="flex:1;">
                            <div class="c-input-box">
                                <div style="display:flex; align-items:center;">
                                    <input type="text" name="comment" id="c-input-'.$pid.'" class="c-input" placeholder="Write a reply..." required>
                                    <label class="c-file-label">
                                        <i class="fa-solid fa-camera"></i>
                                        <input type="file" name="comment_image" style="display:none;" onchange="previewFile(this, \'preview-'.$pid.'\', \'box-'.$pid.'\')">
                                    </label>
                                </div>
                            </div>
                            <div id="box-'.$pid.'" class="img-preview-box" style="display:none;">
                                <img id="preview-'.$pid.'" class="preview-img">
                                <span onclick="clearPreview(\'preview-'.$pid.'\', \'box-'.$pid.'\')" style="position:absolute; top:-5px; right:-5px; background:red; color:white; border-radius:50%; width:15px; height:15px; text-align:center; font-size:10px; cursor:pointer;">x</span>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="padding: 8px 15px; border-radius: 20px;"><i class="fa-solid fa-paper-plane"></i></button>
                    </form>';
                }

                echo '</div></div>';
            }
        } else {
            echo '<p style="text-align:center;">No posts yet.</p>';
        }
        ?>
    </div>

    <?php include 'footer.php'; ?>

    <script>
    function previewFile(input, imgId, boxId) {
        var file = input.files[0];
        if(file){
            var reader = new FileReader();
            reader.onload = function(){
                document.getElementById(imgId).src = reader.result;
                document.getElementById(boxId).style.display = "block";
            }
            reader.readAsDataURL(file);
        }
    }

    function clearPreview(imgId, boxId) {
        document.getElementById(imgId).src = "";
        document.getElementById(boxId).style.display = "none";
    }

    function focusComment(pid) {
        document.getElementById('c-input-'+pid).focus();
    }

    function toggleLike(btn, type, id) {
        const formData = new FormData();
        formData.append('type', type);
        formData.append('id', id);

        fetch('forum_like.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                const span = btn.querySelector('span');
                span.innerText = data.count;
                
                if(data.action === 'liked') {
                    btn.classList.add('liked');
                    if(type === 'post') {
                        btn.querySelector('i').classList.replace('fa-regular', 'fa-solid');
                    }
                } else {
                    btn.classList.remove('liked');
                    if(type === 'post') {
                        btn.querySelector('i').classList.replace('fa-solid', 'fa-regular');
                    }
                }
            } else {
                alert(data.message);
            }
        });
    }
    
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const highlightId = urlParams.get('highlight');
        if (highlightId) {
            setTimeout(function() {
                const element = document.getElementById("f-comment-" + highlightId);
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    element.querySelector('.c-bubble').classList.add('flash-highlight');
                    setTimeout(() => element.querySelector('.c-bubble').classList.remove('flash-highlight'), 3000);
                }
            }, 500);
        }
    });
    </script>
</body>
</html>