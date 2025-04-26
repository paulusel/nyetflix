<?php
// db.php
header("Content-Type: application/json; charset=UTF-8");
define('JWT_SECRET', 'your_very_strong_secret_here'); // Make sure this secret is the same when encoding and decoding the token

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); 
define('DB_PASS', '');
define('DB_NAME', 'netflix_clone');

// JWT Configuration
if (!defined('JWT_SECRET')) define('JWT_SECRET', 'your_very_strong_secret_here');
if (!defined('JWT_ALGO')) define('JWT_ALGO', 'HS256');
if (!defined('JWT_EXPIRE')) define('JWT_EXPIRE', 3600); // 1 hour


try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}
?>
