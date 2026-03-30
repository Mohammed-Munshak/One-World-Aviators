<?php
session_start();
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aviation Colleges - One World Aviators</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .college-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; margin-top: 30px; }
        
        .college-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            border: 1px solid #eee;
        }
        .college-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
        
        .college-banner { height: 180px; width: 100%; object-fit: cover; }
        
        .college-header { 
            padding: 20px; 
            display: flex; 
            align-items: center; 
            gap: 15px; 
            margin-top: -40px; 
        }
        
        .college-icon { 
            width: 70px; 
            height: 70px; 
            border-radius: 50%; 
            border: 4px solid white; 
            background: white; 
            object-fit: contain; 
            box-shadow: 0 3px 6px rgba(0,0,0,0.1); 
        }
        
        /* NEW RATING BADGE STYLE */
        .rating-chip {
            background: #fff;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
            color: #333;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 5px;
            margin-left: auto; /* Pushes it to the right */
            margin-top: 35px; /* Aligns with icon bottom */
        }
        .rating-chip i { color: #ffc107; }
        
        .college-info { padding: 0 20px 20px 20px; flex-grow: 1; }
        .college-name { font-size: 1.3rem; font-weight: bold; color: var(--primary-color); margin-bottom: 5px; }
        .college-desc { font-size: 0.9rem; color: #666; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 15px; }
        
        .btn-details {
            display: block;
            text-align: center;
            background: var(--primary-color);
            color: white;
            padding: 12px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
        }
        .btn-details:hover { background: var(--secondary-color); }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="page-header">
        <div class="container">
            <h1>Aviation Colleges in Sri Lanka</h1>
            <p>Find the best place to start your aviation career</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="college-grid">
                <?php
                // --- MODIFIED SQL: Sort by Rating Highest to Lowest ---
                $sql = "SELECT * FROM aviation_colleges ORDER BY average_rating DESC, college_name ASC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $banner = !empty($row['image_path']) ? $row['image_path'] : 'images/default_college.jpg';
                        $icon = !empty($row['icon_path']) ? $row['icon_path'] : 'images/default_icon.png';
                        
                        // Rating Logic
                        $rating = $row['average_rating'];
                        $rating_display = ($rating > 0) ? $rating : "New";
                        $star_icon = ($rating > 0) ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                        
                        echo '
                        <div class="college-card">
                            <img src="'.$banner.'" class="college-banner" alt="Banner">
                            
                            <div class="college-header">
                                <img src="'.$icon.'" class="college-icon" alt="Logo">
                                
                                <div class="rating-chip">
                                    '.$star_icon.' '.$rating_display.'
                                </div>
                            </div>
                            
                            <div class="college-info">
                                <h3 class="college-name">'.$row['college_name'].'</h3>
                                
                                <div style="font-size:0.85rem; color:#555; margin-bottom:5px;">
                                    <i class="fa-solid fa-location-dot" style="color:var(--secondary-color);"></i> '.$row['location_address'].'
                                </div>
                                <div style="font-size:0.85rem; color:#555; margin-bottom:10px;">
                                    <i class="fa-solid fa-phone" style="color:var(--secondary-color);"></i> '.$row['contact_number'].'
                                </div>

                                <p class="college-desc">'.$row['description'].'</p>
                            </div>
                            
                            <a href="college_details.php?id='.$row['id'].'" class="btn-details">
                                View Details & Stories <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>';
                    }
                } else {
                    echo '<p>No colleges found.</p>';
                }
                ?>
            </div>
        </div>
    </section>
    <?php include 'chatbot.php'; ?>
    <?php include 'footer.php'; ?>
</body>
</html>