<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Profile - Netflix</title>
    <link href="../assets/images/Icon/Icon.ico" rel="icon" type="image/x-icon" />
    <link href="./styles/index.css" rel="stylesheet" type="text/css" />
    <link href="./styles/editProfile.css" rel="stylesheet" type="text/css" />
    <link href="./styles/footer.css" rel="stylesheet" type="text/css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
    <body>
        <header class="simple-header">
            <div class="netflix-logo">
                <a href="profile.php">
                    <img src="../assets/images/Icon/logo.png" alt="Netflix" />
                </a>
            </div>
        </header>

        <main class="edit-profile-container">
            <h1>Edit Profile</h1>
            
            <div class="profile-form">
                <div class="profile-header">
                    <div class="profile-image">
                        <img src="../assets/images/Avatars/default.png" alt="Profile Avatar" id="profileAvatar" />
                        <div class="image-overlay">
                            <i data-feather="edit-2" class="edit-icon"></i>
                        </div>
                    </div>
                    <div class="profile-name">
                        <label for="displayName">Name</label>
                        <input type="text" id="displayName" placeholder="Display Name" value="" />
                    </div>
                </div>

                <div class="divider"></div>

                <div class="section password-section">
                    <h2>Password</h2>
                    <div class="form-group">
                        <label for="password">Current password</label>
                        <input type="password" id="password" placeholder="Current password" />
                    </div>
                    
                    <div class="form-group">
                        <label for="newPassword">New password</label>
                        <input type="password" id="newPassword" placeholder="New password" />
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">Confirm new password</label>
                        <input type="password" id="confirmPassword" placeholder="Confirm new password" />
                    </div>
                </div>

                <div class="divider"></div>

                <div class="form-actions">
                    <button class="btn-save">Save</button>
                    <button class="btn-cancel">Cancel</button>
                </div>
                
                <div class="divider"></div>
                
                <div class="delete-profile-container">
                    <button class="btn-delete-profile">Delete Profile</button>
                </div>
            </div>
        </main>

        <footer class="column">
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
            &copy; 2025 Netflix, Inc.
            </div>
      </footer>

      <script type="module" src="./scripts/script.js"></script>
      <script type="module" src="./scripts/editProfile.js"></script>
      <script src="https://unpkg.com/feather-icons"></script>
      <script>
        feather.replace();
      </script>
    </body>
</html>
