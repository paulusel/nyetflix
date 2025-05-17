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
    <link href="./styles/index.css" rel="stylesheet" type="text/css" />
    <link href="./styles/main.css" rel="stylesheet" type="text/css" />
    <link href="./styles/nav.css" rel="stylesheet" type="text/css" />
    <link href="./styles/content-row.css" rel="stylesheet" type="text/css" />
    <link href="./styles/top.css" rel="stylesheet" type="text/css" />
    <link href="./styles/footer.css" rel="stylesheet" type="text/css" />
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
    </style>
</head>
<body>
    <div id="loading-overlay" class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <header>
        <nav>
            <div class="left">
                <img src="./assets/images/Icon/logo.png" class="brand" />
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

    <script src="https://unpkg.com/feather-icons"></script>
    <script type="module" src="scripts/home.js"></script>
</body>
</html>
