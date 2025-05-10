<?php

require_once "backend/backend.php";

function idetifyUser(bool $require_profile = true) : array {
    require_once "../backend/auth.php";

    $headers = getallheaders();
    $header = $headers['Authorization'] ?? '';

    $token = str_replace('Bearer ', '', $header);
    $token = trim($token);

    $user = Auth::validate($token);
    if(require_profile) {
        if(isset($user['profile_id'])) {
            return Backend::getProfile($user['profile_id']);
        }
        throw new BackendException('profile is not set. create or set one before making this request');
    }

    return Backend::getUser($user['user_id']);
}

function validateRequest() : void {
    if( $_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new BackendException("request method not supported", 400);
    }

    if($_SERVER["CONTENT_TYPE"] !== "application/json") {
        throw new BackendException("expected json body", 400);
    }
}

function sendMessage(string $message, int $statusCode = 400, bool $ok = false) {
    sendJson(["ok" => $ok, "message" => $message], $statusCode);
}

function sendJson(mixed $value, int $statusCode = 200) : void {
    ob_clean();
    header("Content-Type: application/json");
    http_response_code($statusCode);
    echo json_encode($value);
}
