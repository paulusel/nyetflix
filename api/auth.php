<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../backend/backend/backend.php';
require_once __DIR__ . '/../backend/logger.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');

$backend = new Backend();
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'register':
            $data = json_decode(file_get_contents('php://input'), true);
            $backend->addUser($data['username'], $data['password'], $data['role']);
            echo json_encode(["status" => "success", "message" => "User registered Successfully"]);
            break;

        case 'login':
            $data = json_decode(file_get_contents('php://input'), true);
            $user = $backend->verifyUser($data['username'], $data['password']);
            $token = generateJWT($user);
            echo json_encode(["status" => "success", "token" => $token, "user" => $user]);
            break;

        case 'protected':
            $user = validateJWT();
            if ($user) {
                echo json_encode(["status" => "success", "user" => $user]);
            } else {
                http_response_code(401);
                echo json_encode(["status" => "error", "message" => "Unauthorized"]);
            }
            break;

        case 'logout':
            echo json_encode(["status" => "success", "message" => "Logged out"]);
            break;

        default:
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Invalid action"]);
    }
} catch (BackendException $e) {
    http_response_code($e->getCode());
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
} catch (Exception $e) {
    Logger::log($e->getMessage(), "ERROR");
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server error"]);
}

function generateJWT($user)
{
    $payload = [
        'iat' => time(),
        'exp' => time() + 3600,
        'data' => $user
    ];
    return JWT::encode($payload, 'your_secret_key', 'HS256');
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
        $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
        return $decoded->data;
    } catch (Exception $e) {
        return false;
    }
}

?>