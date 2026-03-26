<?php
session_start();
include 'db_connect.php';

$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// HELPER FUNCTION (Defined at top to avoid scope issues)
function renderBtn($nid, $type, $icon, $count, $my_react) {
    $activeClass = ($type == $my_react) ? 'active' : '';
    return '<button class="reaction-btn '.$activeClass.'" onclick="reactToNews('.$nid.', \''.$type.'\', this)" title="'.ucfirst($type).'">
                '.$icon.' <span class="count-'.$type.'">'.$count.'</span>
            </button>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Latest News - One World Aviators</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .news-feed-item {
            display: flex; background: #ffffff; border: 1px solid #e0e0e0;
            border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 25px; overflow: hidden; transition: transform 0.2s;
        }
        .news-feed-image { width: 250px; flex-shrink: 0; background: #f4f4f4; border-right: 1px solid #f0f0f0; }
        .news-feed-image img { width: 100%; height: 100%; object-fit: cover; min-height: 200px; }
        .news-feed-content-wrapper { padding: 25px; flex-grow: 1; display: flex; flex-direction: column; }
        .news-feed-header h2 { font-size: 1.5rem; color: var(--primary-color); margin-bottom: 10px; line-height: 1.3; }
        .news-date { display: block; font-size: 0.85rem; color: #888; margin-bottom: 15px; font-weight: 500; }
        .news-body { font-size: 0.95rem; line-height: 1.6; color: #444; margin-bottom: 20px; }

        /* REACTION BAR STYLES */
        .reaction-bar {
            margin-top: auto;
            border-top: 1px solid #eee;
            padding-top: 15px;
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .reaction-btn {
            background: none; border: none; cursor: pointer;
            font-size: 1.2rem; display: flex; align-items: center; gap: 5px;
            transition: transform 0.2s, filter 0.2s;
            opacity: 0.7;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .reaction-btn:hover { transform: scale(1.2); opacity: 1; background: #f9f9f9; }
        .reaction-btn span { font-size: 0.9rem; font-weight: bold; color: #555; font-family: sans-serif; }
        
        /* Active State */
        .reaction-btn.active { opacity: 1; background: #e3f2fd; border: 1px solid #bbdefb; }
        
        @media (max-width: 768px) {
            .news-feed-item { flex-direction: column; }
            .news-feed-image { width: 100%; height: 200px; }
            .reaction-bar { gap: 5px; justify-content: space-between; }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="page-header">
        <div class="container">
            <h1>Latest Aviation News</h1>
            <p>Stay updated with global and local aviation updates</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container" style="max-width: 1000px;"> 
            <?php
            $sql = "SELECT * FROM latest_news ORDER BY published_date DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $news_id = $row['id'];
                    $img = !empty($row['image_path']) ? $row['image_path'] : 'images/default_news.jpg';
                    
                    // 1. Fetch Date Correctly
                    $date_display = date("F j, Y, g:i a", strtotime($row['published_date']));

                    // 2. Fetch Reaction Counts & User's Current Reaction
                    $reactions = ['like'=>0, 'love'=>0, 'wow'=>0, 'sad'=>0, 'angry'=>0];
                    $my_reaction = '';

                    // Get totals
                    $r_sql = "SELECT reaction_type, COUNT(*) as count FROM news_reactions WHERE news_id='$news_id' GROUP BY reaction_type";
                    $r_res = $conn->query($r_sql);
                    while($r_row = $r_res->fetch_assoc()) {
                        $reactions[$r_row['reaction_type']] = $r_row['count'];
                    }

                    // Get my reaction
                    if ($current_user_id) {
                        $my_r_sql = "SELECT reaction_type FROM news_reactions WHERE news_id='$news_id' AND user_id='$current_user_id'";
                        $my_r_res = $conn->query($my_r_sql);
                        if ($my_r_res->num_rows > 0) {
                            $my_reaction = $my_r_res->fetch_assoc()['reaction_type'];
                        }
                    }

                    echo '
                    <article class="news-feed-item">
                        <div class="news-feed-image">
                            <img src="'.$img.'" alt="'.$row['headline'].'">
                        </div>
                        
                        <div class="news-feed-content-wrapper">
                            <div class="news-feed-header">
                                <h2>'.$row['headline'].'</h2>
                                <span class="news-date"><i class="fa-regular fa-calendar"></i> '.$date_display.'</span>
                            </div>
                            
                            <div class="news-body">
                                <p>'.nl2br($row['content']).'</p>
                            </div>

                            <div class="reaction-bar" id="react-bar-'.$news_id.'">
                                '.renderBtn($news_id, 'like', '👍', $reactions['like'], $my_reaction).'
                                '.renderBtn($news_id, 'love', '❤️', $reactions['love'], $my_reaction).'
                                '.renderBtn($news_id, 'wow',  '😮', $reactions['wow'],  $my_reaction).'
                                '.renderBtn($news_id, 'sad',  '😢', $reactions['sad'],  $my_reaction).'
                                '.renderBtn($news_id, 'angry','😡', $reactions['angry'],$my_reaction).'
                            </div>
                        </div>
                    </article>';
                }
            } else {
                echo '<div class="alert-box"><h3>No news updates available yet.</h3></div>';
            }
            ?>
        </div>
    </section>

    <?php include 'footer.php'; ?>
    
    <script>
    function reactToNews(newsId, type, btnElement) {
        <?php if(!$current_user_id): ?>
            alert("Please login to react to news!");
            window.location.href = 'login.php';
            return;
        <?php endif; ?>

        const formData = new FormData();
        formData.append('news_id', newsId);
        formData.append('type', type);

        fetch('submit_news_reaction.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text()) // Get raw text first
        .then(text => {
            try {
                const data = JSON.parse(text); // Try to parse JSON
                if(data.status === 'success') {
                    // Update Counts
                    const parent = document.getElementById('react-bar-' + newsId);
                    parent.querySelector('.count-like').innerText = data.counts.like;
                    parent.querySelector('.count-love').innerText = data.counts.love;
                    parent.querySelector('.count-wow').innerText = data.counts.wow;
                    parent.querySelector('.count-sad').innerText = data.counts.sad;
                    parent.querySelector('.count-angry').innerText = data.counts.angry;

                    // Update Colors
                    const allBtns = parent.querySelectorAll('.reaction-btn');
                    allBtns.forEach(b => b.classList.remove('active'));

                    if(data.action !== 'removed') {
                        btnElement.classList.add('active');
                    }
                } else {
                    alert("Error: " + data.message);
                }
            } catch (e) {
                // If PHP crashes, it sends HTML, which fails JSON.parse
                // This alert will show you the actual PHP error code
                alert("System Error:\n" + text.substring(0, 200)); 
                console.error(text);
            }
        });
    }
    </script>
</body>
</html>