<?php

require_once "backend/backend.php";

function idetifyUser() : int {
    require_once "../backend/auth.php";

    $authorization = $_SERVER["Authorization"];
    if(is_null($authorization)) {
        throw new BackendException("authorization header not found", 400);
    }

    if(0 !== strpos($authorization, "Bearer ")) {
        throw new BackendException("authorization header not found", 400);
    }

    $token = substr($authorization, 7);
    return Auth::validate($token);
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
    header("Content-Type: application/json");
    http_response_code($statusCode);
    echo json_encode($value);
}
