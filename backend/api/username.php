<?php

if($_SERVER["REQUEST_METHOD"] !== "POST" || $_SERVER["CONTENT_TYPE"] !== "application/json") {
    http_response_code(400);
    exit;
}

$arr = json_decode(file_get_contents("php://input"), true);

if(!isset($arr["username"]) || strlen($arr["username"]) === 0) {
    http_response_code(400);
    exit;
}

include "../backend/backend.php";

$isAvailable = Backend::isUsernameAvailable($arr["username"]);
header("Content-Type: application/json");
echo json_encode(["ok" => true, "available" => $isAvailable]);
