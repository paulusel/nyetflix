<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Validate JWT
function validateJWT() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        return false;
    }
    $authHeader = $headers['Authorization'];
    $token = str_replace('Bearer ', '', $authHeader);

    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET, JWT_ALGO));
        return $decoded->data;
    } catch (Exception $e) {
        return false;
    }
}

// Check if user is admin
function isAdmin($user_data) {
    return isset($user_data->role) && $user_data->role === 'admin';
}

// Router
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));

$resource = array_shift($request);
$id = $request ? intval($request[0]) : null;

if ($resource !== 'movies') {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Resource not found"]);
    exit;
}

// Handle methods
switch ($method) {
    case 'GET':
        if ($id) {
            // Get single movie
            $stmt = $db->prepare("SELECT * FROM movies WHERE id = ?");
            $stmt->execute([$id]);
            $movie = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($movie) {
                echo json_encode(["movie" => $movie]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Movie not found"]);
            }
        } else {
            // Get all movies
            $stmt = $db->query("SELECT * FROM movies ORDER BY created_at DESC");
            $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["status" => "success", "movies" => $movies]);
        }
        break;

    case 'POST':
        $user_data = validateJWT();
        if (!$user_data || !isAdmin($user_data)) {
            http_response_code(403);
            echo json_encode(["status" => "error", "message" => "Access denied"]);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("INSERT INTO movies (title, description, release_year, genre) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $input['title'] ?? '',
            $input['description'] ?? '',
            $input['release_year'] ?? null,
            $input['genre'] ?? '',
        ]);
        echo json_encode(["status" => "success", "message" => "Movie added successfully"]);
        break;

    case 'PUT':
        $user_data = validateJWT();
        if (!$user_data || !isAdmin($user_data)) {
            http_response_code(403);
            echo json_encode(["status" => "error", "message" => "Access denied"]);
            exit;
        }
        $uri = $_SERVER['REQUEST_URI']; // Get the full request URI
    $uriParts = explode('/', $uri); // Split the URI by '/'
    $id = end($uriParts); // The last part should be the ID
        if (!$id) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Movie ID required"]);
            exit;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("SELECT * FROM movies WHERE id = ?");
        $stmt->execute([$id]);
        $movie = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$movie) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Movie not found"]);
        exit;
    }
        $stmt = $db->prepare("UPDATE movies SET title=?, description=?, release_year=?, genre=? WHERE id=?");
        $stmt->execute([
            $title=$input['title'] ?? $movie['title'],
            $description=$input['description'] ?? $movie['description'],
            $release_yeat=$input['release_year'] ?? $movie['release_year'] ,
            $genre=$input['genre'] ?? $movie['genre'],
            $id
        ]);
        echo json_encode(["status" => "success", "message" => "Movie updated successfully"]);
        break;

    case 'DELETE':
        $user_data = validateJWT();
        if (!$user_data || !isAdmin($user_data)) {
            http_response_code(403);
            echo json_encode(["status" => "error", "message" => "Access denied"]);
            exit;
        }
        if (!$id) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Movie ID required"]);
            exit;
        }
        $stmt = $db->prepare("DELETE FROM movies WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["status" => "success", "message" => "Movie deleted successfully"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        break;
}
?>
