<?php
session_start();
require 'db_connect.php';

if (!isset($_GET['id'])) { header("Location: index.php"); exit(); }

$profile_id = mysqli_real_escape_string($conn, $_GET['id']);
$user_sql = "SELECT * FROM users WHERE id = '$profile_id'";
$user_res = $conn->query($user_sql);

if ($user_res->num_rows == 0) { die("User not found."); }

$profile = $user_res->fetch_assoc();

// Profile Picture logic
$view_profile_pic = !empty($profile['profile_pic']) ? $profile['profile_pic'] : 'images/default_avatar.png';

// --- ADMIN CHECK LOGIC ---
$is_admin_user = ($profile['role'] == 'admin');
// Blue Tick for Admin, Gold Tick for Verified Member
if($is_admin_user) {
    $verified_badge = '<i class="fa-solid fa-circle-check" style="color:#00c3ff; margin-left:8px; font-size:0.6em; vertical-align:middle; filter: drop-shadow(0 0 5px rgba(0,195,255,0.5));" title="Verified Administrator"></i>';
} elseif($profile['is_verified_member']) {
    $verified_badge = '<i class="fa-solid fa-circle-check" style="color:#ffc107; margin-left:8px; font-size:0.6em; vertical-align:middle;" title="Verified Member"></i>';
} else {
    $verified_badge = '';
}

// Calculate Age
$age = "N/A";
if(!empty($profile['dob'])) {
    $dob_date = new DateTime($profile['dob']);
    $now = new DateTime();
    $age = $now->diff($dob_date)->y;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $profile['username']; ?> - Profile</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* HEADER SECTION DESIGN */
        .public-profile-header { 
            background: linear-gradient(135deg, #1c2331 0%, #2c3e50 100%); /* Aviation Navy Gradient */
            padding: 60px 20px 80px 20px; 
            text-align: center; 
            color: white;
            position: relative;
            margin-bottom: 60px; /* Space for the floating stats */
        }
        
        .pp-avatar { 
            width: 140px; height: 140px; border-radius: 50%; object-fit: cover; 
            border: 5px solid rgba(255,255,255,0.2); 
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            background: white;
        }
        
        .pp-name { 
            font-size: 2.2rem; margin-top: 15px; margin-bottom: 5px; 
            display: flex; justify-content: center; align-items: center; 
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        
        .pp-profession { 
            font-size: 1.1rem; color: rgba(255,255,255,0.8); margin-bottom: 15px; font-weight: 300; letter-spacing: 1px; text-transform: uppercase;
        }
        
        .admin-label { 
            background: rgba(0, 195, 255, 0.2); border: 1px solid #00c3ff; color: #00c3ff;
            padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; display: inline-block; margin-top: 5px;
        }

        .pp-details { display: flex; justify-content: center; gap: 20px; font-size: 0.95rem; color: rgba(255,255,255,0.7); margin-top: 15px; }
        .pp-details i { color: var(--secondary-color); margin-right: 5px; }
        
        .pp-bio { max-width: 600px; margin: 20px auto; color: rgba(255,255,255,0.9); font-style: italic; font-size: 1.1rem; line-height: 1.6; }

        /* FLOATING STATS BOX */
        .stats-container {
            display: flex; justify-content: center; gap: 30px;
            position: absolute; bottom: -40px; left: 0; right: 0;
        }
        .stat-card {
            background: white; padding: 15px 40px; border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1); text-align: center; min-width: 120px;
            border-bottom: 3px solid var(--secondary-color);
        }
        .stat-num { font-size: 1.4rem; font-weight: 800; color: #2c3e50; }
        .stat-label { font-size: 0.8rem; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }

        .social-links { margin-top: 20px; display: flex; justify-content: center; gap: 10px; }
        .btn-social { 
            width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: white; font-size: 1.2rem; transition: transform 0.2s; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
        }
        .btn-social:hover { transform: translateY(-3px); background: white; }
        .btn-insta:hover { color: #E1306C; }
        .btn-fb:hover { color: #1877F2; }

        /* GALLERY GRID */
        .user-gallery { max-width: 1100px; margin: 80px auto 40px auto; padding: 0 20px; }
        .gallery-header { display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 25px; }
        .gallery-header h3 { margin: 0; font-size: 1.5rem; color: #2c3e50; }
        
        .ug-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 25px; }
        
        .ug-item { 
            background: white; border-radius: 10px; overflow: hidden; 
            box-shadow: 0 3px 10px rgba(0,0,0,0.08); transition: transform 0.3s; 
            text-decoration: none; color: inherit; display: block; border: 1px solid #eee;
        }
        .ug-item:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
        
        .ug-img-wrapper { position: relative; height: 180px; overflow: hidden; }
        .ug-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
        .ug-item:hover img { transform: scale(1.1); }
        
        .ug-info { padding: 15px; }
        .ug-model { font-weight: 800; font-size: 1.1rem; color: var(--primary-color); margin-bottom: 4px; }
        .ug-reg { font-size: 0.9rem; color: #666; font-family: monospace; background: #f0f2f5; padding: 2px 6px; border-radius: 4px; display: inline-block; }
        .ug-airline { font-size: 0.85rem; color: #888; display: block; margin-top: 8px; border-top: 1px solid #eee; padding-top: 8px; }

        @media(max-width: 600px) {
            .stats-container { flex-direction: column; gap: 10px; position: relative; bottom: auto; margin-top: 30px; }
            .stat-card { width: 100%; }
            .user-gallery { margin-top: 40px; }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="public-profile-header">
        <img src="<?php echo $view_profile_pic; ?>" class="pp-avatar">
        
        <h1 class="pp-name">
            <?php echo $profile['username']; ?>
            <?php echo $verified_badge; ?>
        </h1>

        <?php if($is_admin_user): ?>
            <span class="admin-label"><i class="fa-solid fa-shield-halved"></i> Official Administrator</span>
        <?php elseif($profile['profession']): ?>
            <div class="pp-profession"><?php echo $profile['profession']; ?></div>
        <?php endif; ?>
        
        <div class="pp-details">
            <?php if($profile['location']): ?>
                <span><i class="fa-solid fa-location-dot"></i> <?php echo $profile['location']; ?></span>
            <?php endif; ?>
            <?php if($age != "N/A"): ?>
                <span><i class="fa-solid fa-cake-candles"></i> <?php echo $age; ?> Years Old</span>
            <?php endif; ?>
        </div>

        <?php if($profile['bio']): ?>
            <p class="pp-bio">"<?php echo $profile['bio']; ?>"</p>
        <?php endif; ?>

        <?php if($profile['favorite_aircraft']): ?>
             <div style="margin-top:10px; font-size:0.9rem; opacity:0.8;">
                 <i class="fa-solid fa-plane"></i> Favorite: <strong><?php echo $profile['favorite_aircraft']; ?></strong>
             </div>
        <?php endif; ?>

        <div class="social-links">
            <?php if($profile['instagram_handle']): ?>
                <a href="https://instagram.com/<?php echo $profile['instagram_handle']; ?>" target="_blank" class="btn-social btn-insta" title="Instagram">
                    <i class="fa-brands fa-instagram"></i>
                </a>
            <?php endif; ?>
            <?php if($profile['facebook_handle']): ?>
                <a href="https://facebook.com/<?php echo $profile['facebook_handle']; ?>" target="_blank" class="btn-social btn-fb" title="Facebook">
                    <i class="fa-brands fa-facebook"></i>
                </a>
            <?php endif; ?>
        </div>

        <?php
            // Stats
            $photo_count = $conn->query("SELECT COUNT(*) FROM plane_spotters_gallery WHERE user_id='$profile_id' AND status='approved'")->fetch_row()[0];
            
            // FIX: DATE FORMAT WITH DAY (d M Y)
            $join_date = date("d M Y", strtotime($profile['created_at']));
        ?>
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-num"><?php echo $photo_count; ?></div>
                <div class="stat-label">Uploads</div>
            </div>
            <div class="stat-card">
                <div class="stat-num"><?php echo $join_date; ?></div>
                <div class="stat-label">Member Since</div>
            </div>
        </div>
    </div>

    <div class="user-gallery">
        <div class="gallery-header">
            <h3><i class="fa-solid fa-camera"></i> Gallery Uploads</h3>
        </div>
        
        <div class="ug-grid">
            <?php
            $gal_sql = "SELECT * FROM plane_spotters_gallery WHERE user_id='$profile_id' AND status='approved' ORDER BY upload_date DESC";
            $gal_res = $conn->query($gal_sql);

            if ($gal_res->num_rows > 0) {
                while($row = $gal_res->fetch_assoc()) {
                    $airline = !empty($row['airline_name']) ? $row['airline_name'] : 'Unknown Airline';
                    $reg_no = !empty($row['registration_number']) ? $row['registration_number'] : 'N/A';
                    
                    // Link to details page
                    echo '
                    <a href="gallery_details.php?id='.$row['id'].'" class="ug-item">
                        <div class="ug-img-wrapper">
                            <img src="'.$row['image_path'].'" alt="'.$row['aircraft_model'].'">
                        </div>
                        <div class="ug-info">
                            <div class="ug-model">'.$row['aircraft_model'].'</div>
                            <div class="ug-reg"><i class="fa-solid fa-fingerprint"></i> '.$reg_no.'</div>
                            <span class="ug-airline"><i class="fa-solid fa-plane-up"></i> '.$airline.'</span>
                        </div>
                    </a>';
                }
            } else {
                echo '<p style="text-align:center; color:#888; grid-column: 1/-1;">No photos uploaded yet.</p>';
            }
            ?>
        </div>
    </div>
    <?php include 'chatbot.php'; ?>
    <?php include 'footer.php'; ?>
</body>
</html>