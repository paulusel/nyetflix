<?php

require_once __DIR__ . '/../includes.php';

try {
    validateRequest();
    $user = idetifyUser();
    $new_user = json_decode(file_get_contents("php://input"), true);

    if(!$new_user || !is_array($new_user)) {
        sendMessage("no user data found in the request body");
        exit;
    }

    Backend::updateMe($user['user_id'], $new_user);
    sendJson(["ok" => true]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e){
    require '../logger.php';
    Logger::log($e->getMessage());
    sendMessage("internal server error", 500);
}
