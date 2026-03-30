<?php
session_start();
include 'db_connect.php';
$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// --- RECURSIVE FUNCTION FOR Q&A ---
function display_qa($comments, $parent_id = NULL, $level = 0, $current_user_id, $pid, $parent_username = NULL) {
    foreach ($comments as $comment) {
        if ($comment['parent_comment_id'] == $parent_id) {
            
            $margin_left = ($level < 5) ? 30 : 0; 
            
            echo '<div class="qa-box" style="margin-left: '.$margin_left.'px;">
                    <div class="qa-header">
                        <div>
                            <span class="qa-author">'.$comment['username'].'</span>';
                            if ($parent_username) {
                                echo ' <span class="replying-to"><i class="fa-solid fa-share"></i> '.$parent_username.'</span>';
                            }
            echo '      </div>
                        <span class="qa-date">'.date("M j, g:i a", strtotime($comment['created_at'])).'</span>
                    </div>
                    <div class="qa-body">'.$comment['content'].'</div>
                    <div class="qa-actions">';
                        if($current_user_id) {
                            echo '<button class="btn-reply" onclick="toggleReplyForm('.$comment['id'].')">Reply</button>';
                        }
            echo '  </div>';

            if($current_user_id) {
                echo '<form action="submit_interaction.php" method="POST" id="reply-form-'.$comment['id'].'" class="reply-form" style="display:none;">
                        <input type="hidden" name="action" value="add_comment">
                        <input type="hidden" name="program_id" value="'.$pid.'">
                        <input type="hidden" name="parent_id" value="'.$comment['id'].'">
                        <input type="text" name="comment" placeholder="Reply to '.$comment['username'].'..." required>
                        <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                      </form>';
            }

            echo '</div>'; // End qa-box

            display_qa($comments, $comment['id'], $level + 1, $current_user_id, $pid, $comment['username']);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aviation Programs - One World Aviators</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Specific Styles for Program Cards */
        .program-card-full {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 50px;
            overflow: hidden;
        }
        .program-img img { width: 100%; height: 100%; object-fit: cover; }
        .program-details { padding: 30px; }
        .program-details h2 { font-size: 1.8rem; color: var(--primary-color); margin-bottom: 20px; }
        
        .program-info-grid { 
            display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px; 
            background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid var(--secondary-color);
        }
        .info-item { display: flex; align-items: center; gap: 8px; font-size: 0.95rem; color: #555; }
        .info-item i { color: var(--secondary-color); }

        .description { line-height: 1.7; color: #444; margin-bottom: 20px; }
        .special-note { background: #fff0f0; color: #d63031; padding: 10px; border-radius: 5px; display: inline-block; font-size: 0.9rem; }

        /* Q&A Section Styles */
        .qa-section { background: #fafafa; border-top: 1px solid #eee; padding: 20px 30px; }
        .qa-title { font-size: 1.1rem; font-weight: bold; color: #333; margin-bottom: 15px; }
        
        .qa-box { background: white; border: 1px solid #e1e1e1; padding: 10px 15px; border-radius: 8px; margin-bottom: 10px; border-left: 3px solid #007bff; }
        .qa-header { display: flex; justify-content: space-between; font-size: 0.85rem; color: #888; margin-bottom: 5px; }
        .qa-author { font-weight: bold; color: var(--primary-color); }
        .qa-body { font-size: 0.95rem; color: #333; }
        
        .replying-to { background-color: #f0f2f5; color: #65676b; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-left: 8px; }
        .btn-reply { background: none; border: none; color: #007bff; font-size: 0.8rem; cursor: pointer; margin-top: 5px; padding: 0; }
        .btn-reply:hover { text-decoration: underline; }

        .reply-form { margin-top: 10px; display: flex; gap: 5px; }
        .reply-form input { flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .reply-form button { background: var(--primary-color); color: white; border: none; padding: 0 15px; border-radius: 4px; cursor: pointer; }
        
        .main-qa-form { display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; }
        .main-qa-form input { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 20px; }
        .main-qa-form button { background: var(--secondary-color); color: white; border: none; padding: 0 20px; border-radius: 20px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="page-header">
        <div class="container">
            <h1>Aviation Programs & Events</h1>
            <p>Join our upcoming workshops, air shows, and interviews</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="programs-list">
                <?php
                $sql = "SELECT * FROM aviation_programs ORDER BY event_date ASC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $pid = $row['id'];
                        $img = !empty($row['image_path']) ? $row['image_path'] : 'images/default_event.jpg';
                        
                        echo '
                        <div class="program-card-full">
                            <div class="program-img" style="height: 350px;">
                                <img src="'.$img.'" alt="'.$row['title'].'">
                            </div>
                            <div class="program-details">
                                <h2>'.$row['title'].'</h2>
                                <div class="program-info-grid">
                                    <div class="info-item"><i class="fa-regular fa-calendar"></i> <strong>Date:</strong> '.date("M j, Y", strtotime($row['event_date'])).'</div>
                                    <div class="info-item"><i class="fa-regular fa-clock"></i> <strong>Time:</strong> '.date("h:i A", strtotime($row['event_time'])).'</div>
                                    <div class="info-item"><i class="fa-solid fa-location-dot"></i> <strong>Venue:</strong> '.$row['venue'].'</div>
                                </div>
                                <p class="description">'.nl2br($row['description']).'</p>
                                
                                ' . ($row['special_note'] ? '<p class="special-note"><strong>Note: </strong> '.$row['special_note'].'</p>' : '') . '
                            </div>

                            <div class="qa-section">
                                <div class="qa-title"><i class="fa-regular fa-comments"></i> Questions & Discussions</div>';
                                
                                // Fetch comments for this Program
                                $qa_array = [];
                                $comm_sql = "SELECT c.*, u.username FROM comments c 
                                             JOIN users u ON c.user_id = u.id 
                                             WHERE program_id='$pid' ORDER BY created_at ASC";
                                $comm_res = $conn->query($comm_sql);
                                while($c = $comm_res->fetch_assoc()) { $qa_array[] = $c; }

                                if (!empty($qa_array)) {
                                    display_qa($qa_array, NULL, 0, $current_user_id, $pid, NULL);
                                } else {
                                    echo '<p style="font-size:0.9rem; color:#777; font-style:italic;">No questions yet. Be the first to ask!</p>';
                                }

                                // Main Ask Form
                                if ($current_user_id) {
                                    echo '
                                    <form action="submit_interaction.php" method="POST" class="main-qa-form">
                                        <input type="hidden" name="action" value="add_comment">
                                        <input type="hidden" name="program_id" value="'.$pid.'">
                                        <input type="text" name="comment" placeholder="Ask a question about this event..." required>
                                        <button type="submit">Ask Question</button>
                                    </form>';
                                } else {
                                    echo '<p style="margin-top:15px; font-size:0.9rem;"><a href="login.php" style="color:var(--secondary-color); font-weight:bold;">Login</a> to ask a question.</p>';
                                }

                        echo '</div> </div>';
                    }
                } else {
                    echo '<div class="alert-box"><h3>No upcoming events.</h3></div>';
                }
                ?>
            </div>
        </div>
    </section>
    
    <?php include 'chatbot.php'; ?>
    <?php include 'footer.php'; ?>
    <script>
    function toggleReplyForm(commentId) {
        var form = document.getElementById('reply-form-' + commentId);
        if (form.style.display === "none") {
            form.style.display = "flex";
            form.querySelector('input').focus();
        } else {
            form.style.display = "none";
        }
    }
    </script>
</body>
</html>