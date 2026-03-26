<?php
session_start();
require 'db_connect.php';

// SECURITY CHECK
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'home';

// HELPER: Short Number Format (1K, 2K)
function format_number($n) {
    if ($n >= 1000) {
        return round($n / 1000, 1) . 'K';
    }
    return $n;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - OWA</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* --- ADMIN DASHBOARD VARIABLES & RESET --- */
        :root {
            --sidebar-width: 260px;
            --admin-bg: #f0f2f5;
            --card-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        body { background-color: var(--admin-bg); }

        .admin-wrapper { display: flex; min-height: 100vh; }

        /* --- MODERN SIDEBAR --- */
        .sidebar { 
            width: var(--sidebar-width); 
            background: #1c2331; /* Deep Navy */
            color: #b0b3b8; 
            position: fixed; 
            height: 100%; 
            overflow-y: auto; 
            z-index: 100;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 10px;
        }
        .sidebar-header h2 { color: #fff; font-size: 1.3rem; margin: 0; font-weight: 700; letter-spacing: 1px; }
        
        .nav-group-title {
            padding: 15px 25px 5px;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: bold;
            color: #5c6b7f;
            letter-spacing: 1px;
        }

        .sidebar a { 
            display: flex; 
            align-items: center; 
            color: #b0b3b8; 
            padding: 12px 25px; 
            text-decoration: none; 
            font-size: 0.95rem;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .sidebar a i { width: 25px; font-size: 1.1rem; text-align: center; margin-right: 10px; }
        
        .sidebar a:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .sidebar a.active { 
            background: rgba(var(--primary-color-rgb), 0.1); 
            color: #fff; 
            border-left-color: var(--secondary-color);
            background: linear-gradient(90deg, rgba(255,255,255,0.1) 0%, transparent 100%);
        }

        /* --- MAIN CONTENT AREA --- */
        .main-content { 
            flex: 1; 
            padding: 30px 40px; 
            margin-left: var(--sidebar-width); 
            max-width: calc(100% - var(--sidebar-width));
        }

        h1 { color: #2c3e50; font-weight: 800; margin-bottom: 25px; font-size: 2rem; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px; display: inline-block; }

        .card { 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: var(--card-shadow); 
            margin-bottom: 30px; 
            border: 1px solid rgba(0,0,0,0.02);
        }
        
        .card h3 { margin-top: 0; color: var(--primary-color); border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }

        /* --- STATS GRID --- */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 25px; margin-bottom: 30px; }
        
        .stat-card { 
            background: white; padding: 25px; border-radius: 12px; 
            box-shadow: var(--card-shadow); display: flex; align-items: center; justify-content: space-between;
            transition: transform 0.3s; border-bottom: 3px solid transparent;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card.c-blue { border-bottom-color: #3498db; }
        .stat-card.c-green { border-bottom-color: #2ecc71; }
        .stat-card.c-orange { border-bottom-color: #e67e22; }
        .stat-card.c-purple { border-bottom-color: #9b59b6; }

        .stat-info { display: flex; flex-direction: column; }
        .stat-number { font-size: 2.2rem; font-weight: 800; color: #333; line-height: 1; }
        .stat-label { color: #777; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-top: 5px; }
        .stat-icon { font-size: 2.5rem; opacity: 0.15; color: #333; }

        /* --- TABLES --- */
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 10px; }
        .data-table th { background-color: #f8f9fa; color: #555; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; padding: 15px; text-align: left; border-bottom: 2px solid #eee; }
        .data-table td { padding: 15px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; color: #444; }
        .data-table tr:hover td { background-color: #fcfcfc; }
        
        /* --- BUTTONS --- */
        .action-btn { padding: 8px 14px; border-radius: 6px; color: white; font-size: 0.85rem; margin-right: 5px; display:inline-flex; align-items: center; gap: 5px; text-decoration: none; border: none; cursor: pointer; transition: 0.2s; }
        .action-btn:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-blue { background: #3498db; }
        .btn-red { background: #e74c3c; }
        .btn-green { background: #27ae60; }
        .btn-orange { background: #f39c12; }
        .btn-dark { background: #34495e; }
        
        .thumb-img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #eee; }
        
        .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center; }
        .modal-content { background:white; padding:30px; border-radius:10px; width:400px; box-shadow:0 15px 30px rgba(0,0,0,0.2); }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fa-solid fa-plane-up"></i> OWA Admin</h2>
        </div>
        
        <div class="nav-group-title">Core</div>
        <a href="?tab=home" class="<?php echo $tab=='home'?'active':''; ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="?tab=messages" class="<?php echo $tab=='messages'?'active':''; ?>"><i class="fa-solid fa-envelope"></i> Messages</a>
        <a href="?tab=users" class="<?php echo $tab=='users'?'active':''; ?>"><i class="fa-solid fa-users"></i> Manage Users</a>

        <div class="nav-group-title">Content</div>
        <a href="?tab=news" class="<?php echo $tab=='news'?'active':''; ?>"><i class="fa-regular fa-newspaper"></i> News</a>
        <a href="?tab=events" class="<?php echo $tab=='events'?'active':''; ?>"><i class="fa-regular fa-calendar"></i> Events</a>
        <a href="?tab=colleges" class="<?php echo $tab=='colleges'?'active':''; ?>"><i class="fa-solid fa-graduation-cap"></i> Colleges</a>
        <a href="?tab=stories" class="<?php echo $tab=='stories'?'active':''; ?>"><i class="fa-solid fa-trophy"></i> Success Stories</a>

        <div class="nav-group-title">Community</div>
        <a href="?tab=gallery" class="<?php echo $tab=='gallery'?'active':''; ?>"><i class="fa-solid fa-camera"></i> Gallery</a>
        <a href="?tab=comments" class="<?php echo $tab=='comments'?'active':''; ?>"><i class="fa-regular fa-comments"></i> Comments</a>
        
        <div style="margin-top: auto; padding: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
            <a href="index.php" style="color: #fff;"><i class="fa-solid fa-arrow-left"></i> Website</a>
            <a href="logout.php" style="color: #e74c3c;"><i class="fa-solid fa-power-off"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        
        <?php if($tab == 'home'): ?>
            <h1>Dashboard Overview</h1>
            <div class="stats-grid">
                <div class="stat-card c-blue">
                    <div class="stat-info">
                        <?php 
                            $count = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0]; 
                        ?>
                        <span class="stat-number"><?php echo format_number($count); ?></span>
                        <span class="stat-label">Total Users</span>
                    </div>
                    <i class="fa-solid fa-users stat-icon"></i>
                </div>
                
                <div class="stat-card c-orange">
                    <div class="stat-info">
                        <?php 
                            $msg_c = $conn->query("SELECT COUNT(*) FROM contact_messages")->fetch_row()[0]; 
                        ?>
                        <span class="stat-number"><?php echo format_number($msg_c); ?></span>
                        <span class="stat-label">Messages</span>
                    </div>
                    <i class="fa-solid fa-envelope stat-icon"></i>
                </div>

                <div class="stat-card c-green">
                    <div class="stat-info">
                        <?php 
                            $gal_c = $conn->query("SELECT COUNT(*) FROM plane_spotters_gallery WHERE status='approved'")->fetch_row()[0]; 
                        ?>
                        <span class="stat-number"><?php echo format_number($gal_c); ?></span>
                        <span class="stat-label">Gallery Photos</span>
                    </div>
                    <i class="fa-solid fa-camera stat-icon"></i>
                </div>
                
                <div class="stat-card c-purple">
                    <div class="stat-info">
                        <?php 
                            $for_c = $conn->query("SELECT COUNT(*) FROM forum_posts")->fetch_row()[0]; 
                        ?>
                        <span class="stat-number"><?php echo format_number($for_c); ?></span>
                        <span class="stat-label">Forum Posts</span>
                    </div>
                    <i class="fa-solid fa-comments stat-icon"></i>
                </div>
            </div>
        <?php endif; ?>

        <?php if($tab == 'messages'): ?>
            <h1>Inbox Messages</h1>
            <div class="card">
                <table class="data-table">
                    <thead><tr><th>Date</th><th>From</th><th>Subject</th><th>Message Preview</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $msg_sql = "SELECT * FROM contact_messages ORDER BY submitted_at DESC";
                        $msg_res = $conn->query($msg_sql);
                        if($msg_res->num_rows > 0) {
                            while($msg = $msg_res->fetch_assoc()){
                                echo "<tr>
                                    <td>".date("M d, Y", strtotime($msg['submitted_at']))."</td>
                                    <td><strong>{$msg['name']}</strong><br><small>{$msg['email']}</small></td>
                                    <td>{$msg['subject']}</td>
                                    <td>".substr(htmlspecialchars($msg['message']), 0, 50)."...</td>
                                    <td>
                                        <a href='mailto:{$msg['email']}?subject=Re: {$msg['subject']}' class='action-btn btn-blue'>Reply</a>
                                        <a href='admin_process.php?action=delete_message&id={$msg['id']}' class='action-btn btn-red' onclick='return confirm(\"Delete?\")'>Delete</a>
                                    </td>
                                </tr>";
                            }
                        } else { echo "<tr><td colspan='5' style='text-align:center; padding:30px;'>No messages.</td></tr>"; }
                        ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if($tab == 'users'): ?>
            <h1>Manage Users & Admins</h1>

            <div class="card" style="border-left: 5px solid #f39c12;">
                <h3><i class="fa-solid fa-key"></i> Pending Password Requests</h3>
                <?php
                $req_sql = "SELECT r.*, u.username, u.whatsapp_number, u.profile_pic, u.id as uid FROM password_requests r JOIN users u ON r.user_id = u.id";
                $req_res = $conn->query($req_sql);
                if($req_res->num_rows > 0) {
                    echo '<table class="data-table"><thead><tr><th>User</th><th>WhatsApp</th><th>Action</th></tr></thead><tbody>';
                    while($req = $req_res->fetch_assoc()) {
                        $whatsapp_link = "https://wa.me/" . preg_replace('/[^0-9]/', '', $req['whatsapp_number']);
                        echo "<tr>
                                <td><img src='".($req['profile_pic']?$req['profile_pic']:'images/default_avatar.png')."' class='thumb-img' style='vertical-align:middle; margin-right:5px;'> <b>{$req['username']}</b></td>
                                <td><a href='$whatsapp_link' target='_blank' class='action-btn btn-green'>Chat</a></td>
                                <td>
                                    <form action='admin_process.php' method='POST' style='display:inline;'>
                                        <input type='hidden' name='action' value='approve_pass_reset'><input type='hidden' name='request_id' value='{$req['id']}'>
                                        <button type='submit' class='action-btn btn-blue'>Approve</button>
                                    </form>
                                    <a href='admin_process.php?action=reject_pass_reset&id={$req['id']}' class='action-btn btn-red' onclick='return confirm(\"Reject?\")'>Reject</a>
                                </td>
                              </tr>";
                    }
                    echo '</tbody></table>';
                } else { echo '<p style="color:#666;">No pending requests.</p>'; }
                ?>
            </div>

            <div class="card">
                <h3 style="color:#e74c3c;"><i class="fa-solid fa-user-shield"></i> Current Administrators</h3>
                <table class="data-table">
                    <thead><tr><th>Admin User</th><th>Email</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT * FROM users WHERE role='admin' ORDER BY created_at DESC");
                        while($row = $res->fetch_assoc()){
                            $is_me = ($row['id'] == $_SESSION['user_id']);
                            echo "<tr>
                                <td>
                                    <div style='display:flex; align-items:center; gap:10px;'>
                                        <img src='".($row['profile_pic']?$row['profile_pic']:'images/default_avatar.png')."' class='thumb-img'>
                                        <div><span style='font-weight:bold;'>{$row['username']}</span><br><small style='color:#e74c3c;'>Admin</small></div>
                                    </div>
                                </td>
                                <td>{$row['email']}</td>
                                <td>";
                                    if(!$is_me) {
                                        echo "<a href='admin_process.php?action=remove_admin&id={$row['id']}' class='action-btn btn-red' onclick='return confirm(\"Remove admin privileges from this user?\")'><i class='fa-solid fa-user-minus'></i> Remove Admin</a>";
                                    } else {
                                        echo "<span style='color:#ccc;'>(You)</span>";
                                    }
                            echo "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h3><i class="fa-solid fa-users"></i> Registered Members</h3>
                <table class="data-table">
                    <thead><tr><th>User</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT * FROM users WHERE role='user' ORDER BY created_at DESC");
                        while($row = $res->fetch_assoc()){
                            $status = $row['is_verified_member'] ? '<span style="color:green;">Verified</span>' : '<span style="color:red;">Pending</span>';
                            echo "<tr>
                                <td>
                                    <div style='display:flex; align-items:center; gap:10px;'>
                                        <img src='".($row['profile_pic']?$row['profile_pic']:'images/default_avatar.png')."' class='thumb-img'>
                                        <div><a href='public_profile.php?id={$row['id']}' target='_blank' style='font-weight:bold; color:var(--primary-color); text-decoration:none;'>{$row['username']}</a><br><small>{$row['email']}</small></div>
                                    </div>
                                </td>
                                <td>{$status}</td>
                                <td>".date("M d, Y", strtotime($row['created_at']))."</td>
                                <td>
                                    <div style='display:flex; gap:5px;'>";
                                        if(!$row['is_verified_member']) { echo "<a href='admin_process.php?action=verify_user&id={$row['id']}' class='action-btn btn-green' title='Verify'><i class='fa-solid fa-check'></i></a>"; }
                                        // MAKE ADMIN BUTTON
                                        echo "<a href='admin_process.php?action=make_admin&id={$row['id']}' class='action-btn btn-dark' onclick='return confirm(\"Make this user an Admin?\")' title='Make Admin'><i class='fa-solid fa-user-plus'></i> Admin</a>";
                                        
                                        echo "<button onclick='openPassModal({$row['id']}, \"{$row['username']}\")' class='action-btn btn-orange' title='Pass Change'><i class='fa-solid fa-key'></i></button>
                                              <a href='admin_process.php?action=delete_user&id={$row['id']}' class='action-btn btn-red' onclick='return confirm(\"Delete User?\")'><i class='fa-solid fa-trash'></i></a>
                                    </div>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if($tab == 'news'): ?>
            <h1>Manage News</h1>
            <div class="card">
                <h3>Add New Article</h3>
                <form action="admin_process.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_news">
                    <input type="text" name="headline" placeholder="Headline" required class="form-control" style="margin-bottom:10px; width:100%; padding:10px;">
                    <textarea name="content" rows="4" placeholder="Content" required class="form-control" style="margin-bottom:10px; width:100%; padding:10px;"></textarea>
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Upload Image:</label>
                    <input type="file" name="image" required style="margin-bottom:15px;">
                    <button type="submit" class="action-btn btn-blue">Post News</button>
                </form>
            </div>
            <div class="card">
                <h3>Existing News</h3>
                <table class="data-table">
                    <thead><tr><th>Headline</th><th>Date</th><th style="min-width:180px;">Reactions</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT * FROM latest_news ORDER BY published_date DESC");
                        while($row = $res->fetch_assoc()){
                            $nid = $row['id'];
                            
                            // Initialize Counts
                            $reacts = ['like'=>0, 'love'=>0, 'wow'=>0, 'sad'=>0, 'angry'=>0];
                            
                            // Get Specific Counts
                            $q_r = "SELECT reaction_type, COUNT(*) as c FROM news_reactions WHERE news_id='$nid' GROUP BY reaction_type";
                            $res_r = $conn->query($q_r);
                            if($res_r){
                                while($rr = $res_r->fetch_assoc()){
                                    $reacts[$rr['reaction_type']] = $rr['c'];
                                }
                            }
                            $total = array_sum($reacts);
                            
                            $nice_date = date("M d, Y", strtotime($row['published_date']));
                            echo "<tr>
                                <td>{$row['headline']}</td>
                                <td>{$nice_date}</td>
                                <td>
                                    <div style='display:flex; gap:8px; font-size:0.9rem; align-items:center;'>
                                        <span title='Like'>👍 {$reacts['like']}</span>
                                        <span title='Love'>❤️ {$reacts['love']}</span>
                                        <span title='Wow'>😮 {$reacts['wow']}</span>
                                        <span title='Sad'>😢 {$reacts['sad']}</span>
                                        <span title='Angry'>😡 {$reacts['angry']}</span>
                                    </div>
                                    <div style='font-size:0.75rem; color:#888; margin-top:3px;'>
                                        Total: <strong>{$total}</strong>
                                    </div>
                                </td>
                                <td>
                                    <a href='admin_process.php?action=delete_news&id={$row['id']}' class='action-btn btn-red' onclick='return confirm(\"Delete?\")'>Delete</a>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if($tab == 'events'): ?>
            <h1>Manage Events</h1>
            <div class="card">
                <h3>Add New Event</h3>
                <form action="admin_process.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_event">
                    <input type="text" name="title" placeholder="Event Title" required class="form-control" style="margin-bottom:10px; width:100%; padding:10px;">
                    <textarea name="description" placeholder="Description" required class="form-control" style="margin-bottom:10px; width:100%; padding:10px;"></textarea>
                    <div style="display:flex; gap:10px; margin-bottom:10px;">
                        <input type="date" name="date" required style="padding:10px;">
                        <input type="time" name="time" required style="padding:10px;">
                    </div>
                    <input type="text" name="venue" placeholder="Venue" required class="form-control" style="margin-bottom:10px; width:100%; padding:10px;">
                    <input type="text" name="special_note" placeholder="Special Note" class="form-control" style="margin-bottom:10px; width:100%; padding:10px;">
                    <input type="file" name="image" style="margin-bottom:15px;">
                    <button type="submit" class="action-btn btn-blue">Create Event</button>
                </form>
            </div>
            <div class="card">
                <table class="data-table">
                    <thead><tr><th>Title</th><th>Date</th><th>Venue</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT * FROM aviation_programs ORDER BY event_date DESC");
                        while($row = $res->fetch_assoc()){
                            echo "<tr><td>{$row['title']}</td><td>{$row['event_date']}</td><td>{$row['venue']}</td><td><a href='admin_process.php?action=delete_event&id={$row['id']}' class='action-btn btn-red' onclick='return confirm(\"Delete?\")'>Delete</a></td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if($tab == 'colleges'): ?>
            <h1>Manage Colleges</h1>
            <div class="card">
                <h3>Add New College</h3>
                <form action="admin_process.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_college">
                    <input type="text" name="college_name" placeholder="College Name" required class="form-control" style="margin-bottom:10px; width:100%; padding:10px;">
                    <textarea name="description" placeholder="Description" required class="form-control" style="margin-bottom:10px; width:100%; padding:10px;"></textarea>
                    <textarea name="courses" placeholder="Courses List..." class="form-control" style="margin-bottom:10px; width:100%; padding:10px; height:80px;"></textarea>
                    <input type="text" name="address" placeholder="Address" required class="form-control" style="margin-bottom:10px; width:100%; padding:10px;">
                    <input type="text" name="contact1" placeholder="Contact" required class="form-control" style="margin-bottom:10px; width:100%; padding:10px;">
                    <input type="file" name="image" required style="margin-bottom:15px;"> <input type="file" name="icon" required>
                    <button type="submit" class="action-btn btn-blue">Add College</button>
                </form>
            </div>
            <div class="card">
                <table class="data-table"><thead><tr><th>Icon</th><th>Name</th><th>Contact</th><th>Actions</th></tr></thead><tbody>
                <?php $res = $conn->query("SELECT * FROM aviation_colleges"); while($row = $res->fetch_assoc()){ $icn = !empty($row['icon_path'])?$row['icon_path']:'images/default_icon.png'; echo "<tr><td><img src='$icn' class='thumb-img'></td><td>{$row['college_name']}</td><td>{$row['contact_number']}</td><td><a href='admin_process.php?action=delete_college&id={$row['id']}' class='action-btn btn-red' onclick='return confirm(\"Delete?\")'>Delete</a></td></tr>"; } ?>
                </tbody></table>
            </div>
        <?php endif; ?>

        <?php if($tab == 'gallery'): ?>
            <h1>Manage Gallery</h1>
            <div class="card">
                <table class="data-table"><thead><tr><th>Photo</th><th>User</th><th>Model</th><th>Actions</th></tr></thead><tbody>
                <?php $res = $conn->query("SELECT g.*, u.username FROM plane_spotters_gallery g JOIN users u ON g.user_id = u.id ORDER BY upload_date DESC"); while($row = $res->fetch_assoc()){ echo "<tr><td><img src='{$row['image_path']}' class='thumb-img'></td><td>{$row['username']}</td><td>{$row['aircraft_model']}</td><td><a href='admin_process.php?action=delete_gallery_item&id={$row['id']}' class='action-btn btn-red' onclick='return confirm(\"Delete?\")'>Delete</a></td></tr>"; } ?>
                </tbody></table>
            </div>
        <?php endif; ?>

        <?php if($tab == 'stories'): ?>
            <h1>Manage Success Stories</h1>
            <div class="card">
                <table class="data-table"><thead><tr><th>User</th><th>College</th><th>Rating</th><th>Snippet</th><th>Actions</th></tr></thead><tbody>
                <?php $res = $conn->query("SELECT s.*, u.username, c.college_name FROM college_success_stories s JOIN users u ON s.user_id=u.id JOIN aviation_colleges c ON s.college_id=c.id"); while($row = $res->fetch_assoc()){ $rating = '<span style="color:#f39c12; font-weight:bold;">★ ' . $row['rating'] . '</span>'; echo "<tr><td>{$row['username']}</td><td>{$row['college_name']}</td><td>{$rating}</td><td>".substr($row['story_content'],0,40)."...</td><td><a href='admin_process.php?action=delete_story&id={$row['id']}' class='action-btn btn-red' onclick='return confirm(\"Delete?\")'>Delete</a></td></tr>"; } ?>
                </tbody></table>
            </div>
        <?php endif; ?>

        <?php if($tab == 'comments'): ?>
             <h1>Manage Comments</h1>
             <div class="card">
                 <table class="data-table"><thead><tr><th>User</th><th>Content</th><th>Date</th><th>Actions</th></tr></thead><tbody>
                 <?php $res = $conn->query("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id=u.id ORDER BY created_at DESC"); while($row = $res->fetch_assoc()){ echo "<tr><td>{$row['username']}</td><td>".substr($row['content'],0,50)."...</td><td>{$row['created_at']}</td><td><a href='admin_process.php?action=delete_comment&id={$row['id']}' class='action-btn btn-red' onclick='return confirm(\"Delete?\")'>Delete</a></td></tr>"; } ?>
                 </tbody></table>
             </div>
        <?php endif; ?>

    </div>
</div>

<div id="passModal" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0;">Change Password</h3>
        <p>For: <strong id="modal_uname" style="color:var(--primary-color);"></strong></p>
        <form action="admin_process.php" method="POST">
            <input type="hidden" name="action" value="manual_pass_change">
            <input type="hidden" name="user_id" id="modal_uid">
            <label style="display:block; margin-bottom:5px; font-weight:bold;">New Password:</label>
            <input type="text" name="new_pass" required class="form-control" placeholder="New Password..." style="margin-bottom:20px;">
            <div style="text-align:right;">
                <button type="button" onclick="closeModal()" class="action-btn" style="background:#7f8c8d;">Cancel</button>
                <button type="submit" class="action-btn btn-blue">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPassModal(id, username) {
    document.getElementById('passModal').style.display = 'flex';
    document.getElementById('modal_uid').value = id;
    document.getElementById('modal_uname').innerText = username;
}
function closeModal() {
    document.getElementById('passModal').style.display = 'none';
}
</script>

</body>
</html>