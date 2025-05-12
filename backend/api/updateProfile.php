<?php

require_once __DIR__ . '/../includes.php';

try {
    validateRequest();
    $user = idetifyUser(false);

    $profile = json_decode(file_get_contents("php://input"), true);

    if(!$profile || !is_array($profile)) {
        sendMessage("no user data found in the request body");
        exit;
    }

    Backend::updateProfile($profile['profile_id'], $profile);
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
