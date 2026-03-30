<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id = '$user_id'")->fetch_assoc();
$current_pic = !empty($user['profile_pic']) ? $user['profile_pic'] : 'images/default_avatar.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - OWA</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&family=Oswald:wght@500&display=swap" rel="stylesheet">
    
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

    <style>
        .profile-container { max-width: 1000px; margin: 50px auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); display: flex; gap: 40px; align-items: flex-start; }
        
        .profile-left { text-align: center; width: 35%; }
        .profile-pic-large { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-color); margin-bottom: 20px; }
        
        .profile-right { width: 65%; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #555; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .row { display: flex; gap: 15px; }
        .col { flex: 1; }

        /* --- PROFESSIONAL CARD DESIGN --- */
        #membership-card {
            width: 450px; 
            height: 270px;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); 
            border-radius: 16px;
            position: relative;
            font-family: 'Montserrat', sans-serif;
            color: white;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.1);
            /* Important for high-quality capture */
            transform: translateZ(0); 
        }
        .card-accent { position: absolute; top: 0; left: 30px; width: 2px; height: 100%; background: linear-gradient(to bottom, transparent, #d4af37, transparent); }
        .card-header { display: flex; justify-content: space-between; align-items: center; padding: 25px 30px 0 45px; }
        .card-brand { font-family: 'Oswald', sans-serif; font-size: 1.4rem; letter-spacing: 2px; text-transform: uppercase; color: #fff; text-shadow: 0 2px 4px rgba(0,0,0,0.5); }
        .card-logo { height: 35px; filter: drop-shadow(0 2px 2px rgba(0,0,0,0.3)); }
        .card-body { padding: 25px 30px 0 45px; display: flex; gap: 20px; align-items: center; }
        .card-avatar { width: 70px; height: 70px; border-radius: 12px; object-fit: cover; border: 2px solid #d4af37; box-shadow: 0 5px 15px rgba(0,0,0,0.3); background: #fff; }
        .card-user-info h2 { margin: 0; font-size: 1.3rem; text-transform: uppercase; font-weight: 800; letter-spacing: 1px; color: #f0f0f0; }
        .card-user-info p { margin: 2px 0 0; font-size: 0.75rem; color: #d4af37; text-transform: uppercase; font-weight: 600; letter-spacing: 1.5px; }
        .card-footer { position: absolute; bottom: 25px; left: 45px; right: 30px; display: flex; justify-content: space-between; align-items: flex-end; }
        .card-meta { font-size: 0.6rem; color: rgba(255,255,255,0.6); text-transform: uppercase; line-height: 1.6; }
        .card-meta strong { color: white; }

        /* Badge Styles */
        .verified-badge { background: rgba(255, 255, 255, 0.1); padding: 5px 12px; border-radius: 4px; font-size: 0.65rem; text-transform: uppercase; font-weight: bold; letter-spacing: 1px; backdrop-filter: blur(5px); display: flex; align-items: center; gap: 5px; }
        .badge-gold { border: 1px solid #d4af37; color: #d4af37; }
        .badge-blue { border: 1px solid #00c3ff; color: #00c3ff; box-shadow: 0 0 10px rgba(0, 195, 255, 0.2); }

        /* Verification Box */
        .verification-box { background: #fff8e1; border: 1px solid #ffe082; padding: 20px; border-radius: 8px; margin-top: 30px; }
        .v-status-pending { color: #ff9800; font-weight: bold; font-size: 1.1rem; }
        .v-note { font-size: 0.85rem; color: #666; margin-top: 10px; line-height: 1.5; }

        /* Uploads List */
        .uploads-list { display: flex; flex-direction: column; gap: 15px; margin-top: 20px; }
        .upload-item { 
            display: flex; gap: 12px; align-items: flex-start; text-align: left; 
            padding-bottom: 15px; border-bottom: 1px solid #eee; text-decoration: none; color: inherit;
            transition: background 0.2s; padding: 8px; border-radius: 5px;
        }
        .upload-item:hover { background: #f9f9f9; }
        .upload-thumb { width: 70px; height: 70px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd; flex-shrink: 0; }
        .upload-details { font-size: 0.8rem; line-height: 1.4; color: #555; }
        .u-model { font-weight: bold; color: var(--primary-color); display: block; font-size: 0.9rem; margin-bottom: 2px; }
        .u-reg { font-family: monospace; background: #eee; padding: 1px 4px; border-radius: 3px; color: #333; font-size: 0.75rem; }
        .u-date { color: #999; font-size: 0.75rem; margin-top: 2px; display: block; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?> 

    <div class="container">
        <?php if(isset($_GET['msg']) && $_GET['msg']=='req_sent'): ?>
            <div style="background:#d4edda; color:#155724; padding:15px; margin-top:20px; border-radius:5px; text-align:center;">
                Verification request sent successfully! An admin will contact you shortly.
            </div>
        <?php endif; ?>
        <?php if(isset($_GET['msg']) && $_GET['msg']=='missing_whatsapp'): ?>
            <div style="background:#f8d7da; color:#721c24; padding:15px; margin-top:20px; border-radius:5px; text-align:center;">
                Please save your WhatsApp number in the form below before requesting verification.
            </div>
        <?php endif; ?>

        <div class="profile-container">
            <div class="profile-left">
                <img src="<?php echo $current_pic; ?>" alt="Profile Picture" class="profile-pic-large">
                <form action="upload_propic_logic.php" method="POST" enctype="multipart/form-data">
                    <label class="btn btn-secondary" style="font-size: 0.8rem; cursor: pointer;">
                        <i class="fa-solid fa-camera"></i> Change Photo
                        <input type="file" name="profile_image" style="display: none;" onchange="this.form.submit()">
                    </label>
                </form>
                <br>
                <a href="public_profile.php?id=<?php echo $user_id; ?>" class="btn btn-primary" style="font-size:0.8rem;">View As Public</a>
                
                <h4 style="margin-top:30px; border-bottom:2px solid var(--secondary-color); padding-bottom:5px; text-align:left;">My Uploads</h4>
                <div class="uploads-list">
                    <?php
                    $gal_sql = "SELECT id, image_path, aircraft_model, registration_number, airline_name, captured_date 
                                FROM plane_spotters_gallery 
                                WHERE user_id = '$user_id' 
                                ORDER BY upload_date DESC LIMIT 5";
                    $gal_res = $conn->query($gal_sql);

                    if ($gal_res->num_rows > 0) {
                        while($row = $gal_res->fetch_assoc()) {
                            $date = date("M d, Y", strtotime($row['captured_date']));
                            $airline = !empty($row['airline_name']) ? $row['airline_name'] : 'Unknown Airline';
                            
                            echo '
                            <a href="edit_gallery_item.php?id='.$row['id'].'" class="upload-item">
                                <img src="'.$row['image_path'].'" class="upload-thumb">
                                <div class="upload-details">
                                    <span class="u-model">'.$row['aircraft_model'].'</span>
                                    <div><i class="fa-solid fa-plane-up"></i> '.$airline.'</div>
                                    <div style="margin-top:2px;">
                                        <span class="u-reg">'.$row['registration_number'].'</span>
                                    </div>
                                    <span class="u-date"><i class="fa-regular fa-calendar"></i> '.$date.'</span>
                                </div>
                            </a>';
                        }
                    } else {
                        echo '<p style="font-size:0.9rem; color:#888;">No uploads yet.</p>';
                    }
                    ?>
                </div>
            </div>

            <div class="profile-right">
                
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h2 style="color:var(--primary-color); margin:0;">Edit Profile</h2>
                    <?php if($user['role'] == 'admin'): ?>
                        <span style="color:#00c3ff; font-weight:bold; border:1px solid #00c3ff; padding:5px 10px; border-radius:20px; font-size:0.85rem;">
                            <i class="fa-solid fa-circle-check"></i> Admin
                        </span>
                    <?php endif; ?>
                </div>
                
                <form action="update_profile_logic.php" method="POST">
                    <div class="form-group">
                        <label>Profession (Subtitle)</label>
                        <input type="text" name="profession" class="form-control" value="<?php echo $user['profession']; ?>" placeholder="e.g. Student Pilot">
                    </div>

                    <div class="form-group">
                        <label>WhatsApp Number (Required for Verification)</label>
                        <input type="text" name="whatsapp_number" class="form-control" value="<?php echo isset($user['whatsapp_number']) ? $user['whatsapp_number'] : ''; ?>" placeholder="+94 77 123 4567">
                    </div>

                    <div class="form-group">
                        <label>About Me (Bio)</label>
                        <textarea name="bio" rows="3" class="form-control"><?php echo $user['bio']; ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col form-group">
                            <label>Location</label>
                            <input type="text" name="location" class="form-control" value="<?php echo $user['location']; ?>">
                        </div>
                        <div class="col form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="<?php echo $user['dob']; ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col form-group">
                            <label>Instagram</label>
                            <input type="text" name="instagram" class="form-control" value="<?php echo $user['instagram_handle']; ?>">
                        </div>
                        <div class="col form-group">
                            <label>Facebook</label>
                            <input type="text" name="facebook" class="form-control" value="<?php echo $user['facebook_handle']; ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>

                <?php if($user['is_verified_member'] || $user['role'] == 'admin'): ?>
                    
                    <div style="margin-top:40px; border-top:1px solid #eee; padding-top:30px;">
                        <h3 style="color:var(--primary-color); margin-bottom:20px;">
                            <i class="fa-solid fa-id-card"></i> Official Membership Card
                        </h3>
                        
                        <div style="display:flex; flex-direction:column; gap:20px; align-items:center;">
                            
                            <div id="membership-card">
                                <div class="card-accent"></div>
                                <div class="card-header">
                                    <div class="card-brand">One World Aviators Club</div>
                                    <img src="images/logo.png" class="card-logo">
                                </div>
                                <div class="card-body">
                                    <img src="<?php echo $current_pic; ?>" class="card-avatar" crossorigin="anonymous">
                                    <div class="card-user-info">
                                        <h2><?php echo $user['username']; ?></h2>
                                        <p><?php echo $user['profession'] ? $user['profession'] : 'MEMBER'; ?></p>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="card-meta">
                                        <strong>JOINED:</strong> <?php echo date("d M Y", strtotime($user['created_at'])); ?><br>
                                        <strong>ID:</strong> OWA-<?php echo str_pad($user_id, 4, '0', STR_PAD_LEFT); ?><br>
                                        <?php if($user['instagram_handle']) echo "IG: @".$user['instagram_handle']; ?>
                                    </div>
                                    <?php if($user['role'] == 'admin'): ?>
                                        <div class="verified-badge badge-blue"><i class="fa-solid fa-circle-check"></i> Admin Verified</div>
                                    <?php else: ?>
                                        <div class="verified-badge badge-gold">Verified Member</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <button onclick="downloadCard()" class="btn btn-secondary" style="background:#2c3e50;">
                                <i class="fa-solid fa-download"></i> Download Membership Card
                            </button>
                        </div>
                    </div>

                <?php else: ?>
                    
                    <div class="verification-box">
                        <div style="display:flex; align-items:flex-start; gap:15px;">
                            <i class="fa-solid fa-shield-halved" style="font-size:2rem; color:#ff9800;"></i>
                            <div>
                                <span class="v-status-pending">Verification Required</span>
                                <p class="v-note">
                                    To access exclusive features and get your Membership Card, you must verify your profile.
                                    <br>1. Ensure your <strong>WhatsApp Number</strong> is saved above.
                                    <br>2. Click the button below to notify Admins.
                                    <br>3. Admins will contact you within 3 days.
                                </p>
                                <?php if(!empty($user['whatsapp_number'])): ?>
                                    <a href="request_verification.php" class="btn btn-primary" style="margin-top:10px; background:#ff9800; border:none;">Request Verification</a>
                                <?php else: ?>
                                    <p style="color:red; font-weight:bold; font-size:0.9rem; margin-top:10px;">* Please save your WhatsApp number first.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>

    <script>
    function downloadCard() {
        const card = document.getElementById('membership-card');
        
        // Configuration for High Quality
        const options = {
            backgroundColor: null,
            scale: 5,           // 5x Scale = Ultra High Resolution
            useCORS: true,      // Essential for loading profile pics from URLs
            allowTaint: true,   // Allows cross-origin images
            logging: false
        };

        html2canvas(card, options).then(canvas => {
            var link = document.createElement('a');
            link.download = 'One World Aviators Membership Card.jpg';
            // Save as JPEG with 1.0 (100%) Quality
            link.href = canvas.toDataURL("image/jpeg", 1.0);
            link.click();
        });
    }
    </script>
    <?php include 'chatbot.php'; ?>
</body>
</html>