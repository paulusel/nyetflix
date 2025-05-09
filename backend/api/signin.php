<?php

require_once __DIR__ . '/../includes.php';
require_once __DIR__ . '/../backend/auth.php';

try {
    validateRequest();
    $user = json_decode(file_get_contents("php://input"), true);

    if(!$user || !is_array($user)) {
        sendMessage("no user data found in the request body", 400);
        exit;
    }

    $user = Backend::signin($user);
    $token = Auth::newToken($user["user_id"]);
    sendJson(["ok" => true, "token" => $token, "user" => $user]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e){
    require '../logger.php';
    Logger::log($e->getMessage());
    sendMessage("internal server error", 500);
}
