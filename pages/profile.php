<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netflix</title>
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="icon" href="./assets/images/Icon/Icon.ico" type="image/icon type">
</head>
<body>
    <div class="header-bar"></div>
    <div class="content-wrapper">
        <div class="profile-list">
            <h1 class="profile-gate-label">Who's watching?</h1>
            <ul>
                <li class="user-profile"><div><a class="profile-link" href="home.php" onclick="selectProfile('Profile1')"><div class="avatar-wrapper"><img class="profile-avatar" src="./assets/images/Profile/Profile1.png" alt="Profile1"><span class="profile-name">Paulos</span></div></a></div></li>
                <li class="user-profile"><div><a class="profile-link" href="home.php" onclick="selectProfile('Profile2')"><div class="avatar-wrapper"><img class="profile-avatar" src="./assets/images/Profile/Profile2.png" alt="Profile2"><span class="profile-name">Robel</span></div></a></div></li>
                <li class="user-profile"><div><a class="profile-link" href="home.php" onclick="selectProfile('Profile3')"><div class="avatar-wrapper"><img class="profile-avatar" src="./assets/images/Profile/Profile3.png" alt="Profile3"><span class="profile-name">Yohannes</span></div></a></div></li>
                <li class="user-profile"><div><a class="profile-link" href="home.php" onclick="selectProfile('Profile4')"><div class="avatar-wrapper"><img class="profile-avatar" src="./assets/images/Profile/Profile4.png" alt="Profile4"><span class="profile-name">Yonatan</span></div></a></div></li>
                <li class="user-profile"><div><a class="profile-link" href="home.php" onclick="selectProfile('Profile5')"><div class="avatar-wrapper"><img class="profile-avatar" src="./assets/images/Profile/Profile5.png" alt="Profile5"><span class="profile-name">Zerubbabel</span></div></a></div></li>
            </ul>
        </div>
	<button class="manage-button">Manage Profiles</button>
    </div>
    
    <script src="./scripts/script.js"></script>
    
</body>
</html>
