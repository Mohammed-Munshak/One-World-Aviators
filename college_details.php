<?php
session_start();
require 'db_connect.php';

if (!isset($_GET['id'])) { header("Location: colleges.php"); exit(); }
$cid = $_GET['id'];
$col_sql = "SELECT * FROM aviation_colleges WHERE id='$cid'";
$col_res = $conn->query($col_sql);
if ($col_res->num_rows == 0) { die("College not found."); }
$college = $col_res->fetch_assoc();

$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $college['college_name']; ?> - Details</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .cd-banner-area { position: relative; height: 300px; background: url('<?php echo $college['image_path']; ?>') center/cover no-repeat; }
        .cd-overlay { position: absolute; top:0; left:0; width:100%; height:100%; background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.8)); }
        .cd-header { position: absolute; bottom: 30px; left: 0; width: 100%; padding: 0 40px; display: flex; align-items: flex-end; gap: 20px; }
        .cd-logo { width: 100px; height: 100px; border-radius: 10px; background: white; padding: 5px; object-fit: contain; }
        .cd-title h1 { color: white; font-size: 2.5rem; text-shadow: 0 2px 5px rgba(0,0,0,0.5); margin: 0; }
        
        /* RATING BADGE */
        .rating-badge { background: #ffc107; color: #000; padding: 5px 10px; border-radius: 5px; font-weight: bold; font-size: 1rem; display: inline-flex; align-items: center; gap: 5px; margin-top: 5px; }
        
        .cd-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 40px; margin-top: 40px; }
        .info-box { background: white; border: 1px solid #eee; padding: 25px; border-radius: 10px; margin-bottom: 30px; }
        .info-box h3 { border-bottom: 2px solid var(--secondary-color); padding-bottom: 10px; margin-bottom: 15px; color: var(--primary-color); }
        .info-list li { margin-bottom: 10px; color: #555; display: flex; align-items: center; gap: 10px; }
        
        .story-card { background: #f9f9f9; padding: 20px; border-radius: 8px; border-left: 4px solid #28a745; margin-bottom: 20px; transition: all 0.5s ease; position:relative; }
        .story-header { display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 10px; }
        .story-rating { color: #ffc107; font-size: 0.8rem; }
        .story-author { font-weight: bold; color: var(--primary-color); text-decoration: none; }
        .story-content { font-style: italic; color: #444; line-height: 1.6; }
        .story-actions { margin-top: 10px; display: flex; gap: 15px; font-size: 0.9rem; }
        
        /* Interactive Star Rating CSS */
        .star-rating { direction: rtl; display: inline-flex; }
        .star-rating input { display: none; }
        .star-rating label { font-size: 1.5rem; color: #ddd; cursor: pointer; transition: color 0.2s; }
        .star-rating input:checked ~ label { color: #ffc107; }
        .star-rating label:hover, .star-rating label:hover ~ label { color: #ffc107; }

        /* Highlight Animation */
        .flash-highlight { background-color: #d4edda !important; border: 2px solid #28a745 !important; transform: scale(1.02); z-index: 10; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="cd-banner-area">
        <div class="cd-overlay"></div>
        <div class="container cd-header">
            <img src="<?php echo $college['icon_path'] ? $college['icon_path'] : 'images/default_icon.png'; ?>" class="cd-logo">
            <div class="cd-title">
                <h1><?php echo $college['college_name']; ?></h1>
                
                <div class="rating-badge">
                    <i class="fa-solid fa-star"></i> <?php echo $college['average_rating'] > 0 ? $college['average_rating'] . " / 5.0" : "No ratings yet"; ?>
                </div>
                
                <div style="margin-top:5px;">
                    <?php if($college['website_url']) echo '<a href="'.$college['website_url'].'" target="_blank" style="color:#ddd; text-decoration:none;"><i class="fa-solid fa-globe"></i> Visit Website</a>'; ?>
                </div>
            </div>
        </div>
    </div>

    <section class="content-section">
        <div class="container">
            <div class="cd-grid">
                <div class="main-info">
                    <div class="info-box">
                        <h3>About the College</h3>
                        <p><?php echo nl2br($college['description']); ?></p>
                    </div>

                    <div class="info-box">
                        <h3><i class="fa-solid fa-trophy"></i> Student Success Stories & Reviews</h3>
                        
                        <?php if($current_user_id): ?>
                            <form action="submit_story.php" method="POST" style="margin-bottom: 30px; background: #f0f8ff; padding: 20px; border-radius: 8px;">
                                <h4 style="margin-top:0;">Write a Review</h4>
                                <input type="hidden" name="college_id" value="<?php echo $cid; ?>">
                                
                                <div style="margin-bottom:10px;">
                                    <label style="display:block; font-weight:bold; font-size:0.9rem;">Your Rating:</label>
                                    <div class="star-rating">
                                        <input type="radio" id="star5" name="rating" value="5" required /><label for="star5" title="Excellent"><i class="fa-solid fa-star"></i></label>
                                        <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="Good"><i class="fa-solid fa-star"></i></label>
                                        <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="Average"><i class="fa-solid fa-star"></i></label>
                                        <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="Poor"><i class="fa-solid fa-star"></i></label>
                                        <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="Terrible"><i class="fa-solid fa-star"></i></label>
                                    </div>
                                </div>

                                <textarea name="story" rows="3" style="width:100%; border:1px solid #ccc; border-radius:5px; padding:10px;" placeholder="Share your experience..." required></textarea>
                                <button type="submit" class="btn btn-primary" style="margin-top:10px; font-size:0.9rem;">Submit Review</button>
                            </form>
                        <?php else: ?>
                            <p style="margin-bottom:20px;"><em><a href="login.php">Login</a> to share your review!</em></p>
                        <?php endif; ?>

                        <?php
                        $story_sql = "SELECT s.*, u.username, u.profile_pic FROM college_success_stories s 
                                      JOIN users u ON s.user_id = u.id 
                                      WHERE college_id='$cid' ORDER BY created_at DESC";
                        $stories = $conn->query($story_sql);

                        if($stories->num_rows > 0) {
                            while($story = $stories->fetch_assoc()) {
                                $sid = $story['id'];
                                // Generate Star Display for this story
                                $stars = "";
                                for($i=1; $i<=5; $i++) {
                                    if($i <= $story['rating']) { $stars .= '<i class="fa-solid fa-star"></i>'; } 
                                    else { $stars .= '<i class="fa-regular fa-star"></i>'; }
                                }

                                echo '
                                <div class="story-card" id="story-'.$sid.'">
                                    <div class="story-header">
                                        <div>
                                            <a href="public_profile.php?id='.$story['user_id'].'" class="story-author">
                                                <i class="fa-solid fa-user-graduate"></i> '.$story['username'].'
                                            </a>
                                            <span style="color:#888; margin-left:10px; font-size:0.8rem;">'.date("M Y", strtotime($story['created_at'])).'</span>
                                        </div>
                                        <div class="story-rating">'.$stars.'</div>
                                    </div>
                                    <p class="story-content">"'.$story['story_content'].'"</p>
                                </div>';
                            }
                        } else {
                            echo '<p>No reviews yet.</p>';
                        }
                        ?>
                    </div>
                </div>

                <div class="sidebar-info">
                    <div class="info-box">
                        <h3>Contact Info</h3>
                        <ul class="info-list" style="list-style:none; padding:0;">
                            <li><i class="fa-solid fa-phone"></i> <?php echo $college['contact_number']; ?></li>
                            <li><i class="fa-solid fa-envelope"></i> <?php echo $college['email']; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
    <script>
    // Highlight Logic
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const highlightId = urlParams.get('highlight');
        if (highlightId) {
            setTimeout(function() {
                const element = document.getElementById("story-" + highlightId);
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    element.classList.add('flash-highlight');
                    setTimeout(() => element.classList.remove('flash-highlight'), 3000);
                    window.history.replaceState({}, document.title, window.location.href.split('&highlight')[0]);
                }
            }, 500);
        }
    });
    </script>
</body>
</html>