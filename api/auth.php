<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../vendor/autoload.php'; // Load Composer autoload

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'register':
        $data = json_decode(file_get_contents("php://input"), true);
        $response = handleRegister($db, $data);
        echo json_encode($response);
        break;

    case 'login':
        $data = json_decode(file_get_contents("php://input"), true);
        $response = handleLogin($db, $data);
        echo json_encode($response);
        break;

    case 'protected':
        $user_data = validateJWT();
        $response = handleProtected($user_data);
        echo json_encode($response);
        break;
    case 'logout':
        $response = handleLogout();
        echo json_encode($response);
        break;

    default:
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid action specified.'
        ]);
        break;
}

// Helper functions
function sanitizeInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateJWT($user_id, $username,$role)
{
    $issued_at = time();
    $expiration_time = $issued_at + JWT_EXPIRE;

    $payload = [
        'iat' => $issued_at,
        'exp' => $expiration_time,
        'iss' => 'auth_api',
        'data' => [
            'user_id' => $user_id,
            'username' => $username,
            'role' => $role
        ]
    ];

    return JWT::encode($payload, JWT_SECRET, JWT_ALGO);
}

function validateJWT()
{
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        return false;
    }

    $authHeader = $headers['Authorization'];
    $token = str_replace('Bearer ', '', $authHeader);

    try {
        // Decode the token using the secret key
        $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
        return $decoded->data; // Return the decoded user data
    } catch (Exception $e) {
        return false;
    }
}

// Register function
function handleRegister($db, $data)
{
    $required = ['username', 'password', 'role'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            return ["status" => "error", "message" => "$field is required"];
        }
    }

    $username = sanitizeInput($data['username']);
    $password = sanitizeInput($data['password']);
    $role = sanitizeInput($data['role']);

    // Validate role
    $validRoles = ['user', 'admin']; // Add your roles here
    if (!in_array($role, $validRoles)) {
        http_response_code(400);
        return ["status" => "error", "message" => "Invalid role specified"];
    }

    // Check if username exists
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        http_response_code(409);
        return ["status" => "error", "message" => "Username already exists"];
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user with role
    $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $hashedPassword, $role]);

    if ($stmt->rowCount() > 0) {
        $user_id = $db->lastInsertId();
        return [
            "status" => "success",
            "message" => "User registered successfully",
            "user" => [
                "id" => $user_id,
                "username" => $username,
                "role" => $role
            ]
        ];
    }

    http_response_code(500);
    return ["status" => "error", "message" => "Registration failed"];
}
// Login function
function handleLogin($db, $data)
{
    $username = sanitizeInput($data['username'] ?? '');
    $password = sanitizeInput($data['password'] ?? '');

    if (empty($username) || empty($password)) {
        http_response_code(400);
        return ["status" => "error", "message" => "Username and password are required"];
    }

    $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->rowCount() === 0) {
        http_response_code(401);
        return ["status" => "error", "message" => "Invalid credentials"];
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (password_verify($password, $user['password'])) {
        $token = generateJWT($user['id'], $user['username'], $user['role']);
        return [
            "status" => "success",
            "message" => "Login successful",
            "token" => $token,
            "user" => [
                "id" => $user['id'],
                "username" => $user['username'],
                "role" => $user['role']
            ]
        ];
    }

    http_response_code(401);
    return ["status" => "error", "message" => "Invalid credentials"];
}

// Logout function
function handleLogout()
{
    // With JWT, logout is handled client-side by discarding the token
    return ["status" => "success", "message" => "Logged out successfully"];
}

// Protected content function
function handleProtected($user_data, $required_role = 'admin')
{
    if (!$user_data) {
        http_response_code(401);
        return ["status" => "error", "message" => "Unauthorized"];
    }

    // Check role if specified
    if ($required_role && $user_data->role !== $required_role) {
        http_response_code(403);
        return ["status" => "error", "message" => "Insufficient permissions"];
    }

    return [
        "status" => "success",
        "message" => "Access granted",
        "user" => [
            "id" => $user_data->user_id,
            "username" => $user_data->username,
            "role" => $user_data->role
        ]
    ];
}
?>