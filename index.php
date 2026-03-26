<?php
// 1. Start Session and Connect to Database
session_start();
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One World Aviators Club - Flying Towards Tomorrow</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* =========================================
           HOME PAGE SPECIFIC STYLES
           ========================================= */
        
        /* 1. HERO SECTION WITH IMAGE BACKGROUND */
        .hero-section {
            /* Make sure 'home-hero.jpg' exists in your images folder */
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('images/home-hero.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            padding: 150px 20px;
            text-align: center;
            border-radius: 0 0 20px 20px;
        }

        .hero-content h2 {
            font-size: 3rem;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
        
        .hero-content p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 30px auto;
            line-height: 1.6;
            text-shadow: 0 1px 2px rgba(0,0,0,0.5);
        }

        /* 2. NEW EVENT CARD DESIGN */
        .event-card-home {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            display: flex;
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid #eee;
            transition: transform 0.3s ease;
        }

        .event-card-home:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .event-home-img {
            width: 250px;
            flex-shrink: 0;
            position: relative;
        }

        .event-home-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Date Badge overlaid on image */
        .date-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255, 255, 255, 0.95);
            padding: 8px 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .db-month { display: block; font-size: 0.8rem; font-weight: bold; color: #555; text-transform: uppercase; }
        .db-day { display: block; font-size: 1.5rem; font-weight: bold; color: var(--primary-color); line-height: 1; }

        .event-home-details {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .event-home-details h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #333;
        }

        .event-meta-home {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #666;
        }

        .event-meta-home i { color: var(--secondary-color); margin-right: 5px; }

        /* Responsive */
        @media (max-width: 768px) {
            .event-card-home { flex-direction: column; }
            .event-home-img { width: 100%; height: 200px; }
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <section class="hero-section">
        <div class="container hero-content">
            <h2>Welcome to One World Aviators Club</h2>
            <p>
                One World Aviators Club is dedicated to guiding all Sri Lankans on their aviation journey: 
                connecting enthusiasts, future aviators, and professionals with the knowledge and support they need.
            </p>
            <a href="about.php" class="btn btn-primary" style="padding: 12px 30px; font-size: 1.1rem;">Learn More</a>
        </div>
    </section>

    <section class="latest-news-section">
        <div class="container">
            <div class="section-header">
                <h2>Latest News</h2>
            </div>
            
            <div class="news-grid">
                <?php
                $news_sql = "SELECT * FROM latest_news ORDER BY published_date DESC LIMIT 3";
                $news_result = $conn->query($news_sql);

                if ($news_result->num_rows > 0) {
                    while($row = $news_result->fetch_assoc()) {
                        $img_src = !empty($row['image_path']) ? $row['image_path'] : 'images/default_news.jpg';
                        echo '
                        <article class="news-card">
                            <div class="news-image">
                                <img src="'.$img_src.'" alt="'.$row['headline'].'">
                            </div>
                            <div class="news-content">
                                <span class="news-date"><i class="fa-regular fa-calendar"></i> '.date("F j, Y", strtotime($row['published_date'])).'</span>
                                <h3>'.$row['headline'].'</h3>
                                <p>'.nl2br($row['content']).'</p>
                            </div>
                        </article>';
                    }
                } else {
                    echo '<p class="no-data">No news updates available at the moment. Stay tuned!</p>';
                }
                ?>
            </div>

            <div class="view-all-container">
                <a href="news.php" class="btn-view-all">
                    View All Aviation News <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="shortcuts-section">
        <div class="container">
            <div class="shortcuts-grid">
                <div class="shortcut-card">
                    <div class="icon-box"><i class="fa-solid fa-graduation-cap"></i></div>
                    <h3>Aviation Colleges</h3>
                    <p>Gather all aviation colleges and course details from Sri Lanka.</p>
                    <a href="colleges.php" class="btn btn-secondary">Take Off</a>
                </div>
                <div class="shortcut-card">
                    <div class="icon-box"><i class="fa-solid fa-camera"></i></div>
                    <h3>Plane Spotters Community</h3>
                    <p>The place where all the spotters gather around Sri Lanka.</p>
                    <a href="gallery.php" class="btn btn-secondary">Take Off</a>
                </div>
                <div class="shortcut-card">
    <div class="icon-box"><i class="fa-solid fa-comments"></i></div>
    
    <h3>The Hangar</h3>
    
    <p>Ask questions, share aviation insights, and discuss with the aviation community.</p>
    
    <a href="community.php" class="btn btn-secondary">Enter Hangar</a>
</div>
            </div>
        </div>
    </section>

    <section class="programs-section">
        <div class="container">
            <h2>Upcoming Aviation Programs & Events</h2>
            
            <div class="events-list">
                <?php
                // Fetch next 2 upcoming events
                $event_sql = "SELECT * FROM aviation_programs WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 2";
                $event_result = $conn->query($event_sql);

                if ($event_result->num_rows > 0) {
                    while($row = $event_result->fetch_assoc()) {
                        $e_img = !empty($row['image_path']) ? $row['image_path'] : 'images/default_event.jpg';
                        $e_date_month = date("M", strtotime($row['event_date']));
                        $e_date_day = date("d", strtotime($row['event_date']));

                        echo '
                        <div class="event-card-home">
                            <div class="event-home-img">
                                <img src="'.$e_img.'" alt="'.$row['title'].'">
                                <div class="date-badge">
                                    <span class="db-month">'.$e_date_month.'</span>
                                    <span class="db-day">'.$e_date_day.'</span>
                                </div>
                            </div>
                            
                            <div class="event-home-details">
                                <h3>'.$row['title'].'</h3>
                                
                                <div class="event-meta-home">
                                    <span><i class="fa-regular fa-clock"></i> '.date("h:i A", strtotime($row['event_time'])).'</span>
                                    <span><i class="fa-solid fa-location-dot"></i> '.$row['venue'].'</span>
                                </div>
                                
                                <p style="color:#555; font-size:0.95rem; margin-bottom:15px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    '.$row['description'].'
                                </p>
                                
                                <a href="programs.php" class="btn btn-secondary" style="align-self: flex-start;">Board Now</a>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p class="no-data">No upcoming events scheduled. Check back soon!</p>';
                }
                ?>
            </div>
            
            <div class="center-btn">
                <a href="programs.php" class="view-all-link">View All Programs <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

</body>
</html>