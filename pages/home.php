<?php

require_once __DIR__ . '/../backend/helpers.php';

try {
    $user = idetifyUser(false);
    if(!isset($user['profile'])) {
        header("Location: profile.php");
    }
}
catch(Exception $e) {
    header("Location: signin.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Netflix</title>
    <link href="./assets/images/Icon/Icon.ico" rel="icon" type="image/x-icon" />
    <link href="styles/index.css" rel="stylesheet" type="text/css" />
    <link href="styles/main.css" rel="stylesheet" type="text/css" />
    <link href="styles/nav.css" rel="stylesheet" type="text/css" />
    <link href="styles/content-row.css" rel="stylesheet" type="text/css" />
    <link href="styles/top.css" rel="stylesheet" type="text/css" />
    <link href="styles/footer.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #141414;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid #333;
            border-top: 3px solid #e50914;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .error-message {
            background-color: #e50914;
            color: white;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            text-align: center;
        }
        .video-player {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #000;
            z-index: 10000;
            display: none;
        }
        .video-player video {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .video-controls {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
            display: flex;
            align-items: center;
            gap: 20px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .video-player:hover .video-controls {
            opacity: 1;
        }
        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            color: white;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            z-index: 10001;
        }
    </style>
</head>
<body>
    <div id="loading-overlay" class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <div id="video-player" class="video-player">
        <button class="close-btn">&times;</button>
        <video id="video" controls></video>
        <div class="video-controls">
            <button class="play-pause">
                <i class="fa fa-play"></i>
            </button>
            <div class="progress-bar">
                <div class="progress"></div>
            </div>
            <div class="time">0:00 / 0:00</div>
            <button class="fullscreen">
                <i class="fa fa-expand"></i>
            </button>
        </div>
    </div>

    <header>
        <nav>
            <div class="left">
                <img src="assets/images/Icon/logo.png" class="brand" />
                <div class="nav-item small">Browse</div>
                <ul class="nav-items">
                    <a href="home.php" class="nav-items"><li class="nav-item">Home</li></a>
                    <a href="TvSeries.php" class="nav-items"><li class="nav-item">TV Shows</li></a>
                    <a href="#" class="nav-items"><li class="nav-item">Movies</li></a>
                    <a href="#" class="nav-items"><li class="nav-item">Latest</li></a>
                    <a href="#" class="nav-items"><li class="nav-item">My List</li></a>
                    <a href="#" class="nav-items"><li class="nav-item">Browse by Languages</li></a>
                </ul>
            </div>
            <div class="right">
                <ul class="nav-icons">
                    <div class="nav-item icon">
                        <i data-feather="search"></i>
                    </div>
                    <div class="nav-item icon">
                        <i data-feather="bell"></i>
                    </div>
                    <div class="nav-item icon">
                        <img alt="profile icon" />
                        <i class="hide" data-feather="chevron-down"></i>
                    </div>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <!-- Hero banner section -->
        <div id="hero-banner" class="top">
            <!-- Will be populated by JavaScript -->
        </div>

        <!-- Content rows will be added here by JavaScript -->
        <div id="content-rows"></div>
    </main>

    <footer class="column">
        <div class="social-icons row">
          <i class="fa fa-facebook social-icon"></i>
          <i class="fa fa-instagram social-icon"></i>
          <i class="fa fa-youtube social-icon"></i>
        </div>

        <div class="footer-links">
            <a href="#" class="footer-link">Audio Description</a>
            <a href="#" class="footer-link">Help Center</a>
            <a href="#" class="footer-link">Gift Cards</a>
            <a href="#" class="footer-link">Media Center</a>
            <a href="#" class="footer-link">Investor Relations</a>
            <a href="#" class="footer-link">Jobs</a>
            <a href="#" class="footer-link">Terms of use</a>
            <a href="#" class="footer-link">Privacy</a>
            <a href="#" class="footer-link">Legal Notices</a>
            <a href="#" class="footer-link">Cookie Preferences</a>
            <a href="#" class="footer-link">Coorperate Information</a>
            <a href="#" class="footer-link">Contact us</a>
        </div>

        <div class="footer-service">
            <button class="service-code">Service Code</button>
        </div>

            <div class="footer-copyright">
            &copy; 2025 Nyetflix, Inc.
            </div>
      </footer>


    <script src="https://unpkg.com/feather-icons"></script>
    <script src="scripts/hls.min.js"></script>
    <script type="module" src="scripts/home.js"></script>
</body>
</html>
