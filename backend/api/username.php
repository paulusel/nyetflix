<?php

require_once __DIR__ . '../includes.php';

try {
    $username = json_decode(file_get_contents("php://input"), true);

    if(!$username || !is_string($username)) {
        sendMessage("username not specified in request", 400);
        exit;
    }

    $isAvailable = Backend::isUserNameAvailable($username);
    sendJson(["ok" => true, "available" => $isAvailable]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e){
    require '../logger.php';
    Logger::log($e->getMessage);
    sendMessage("internal server error", 500);
}
