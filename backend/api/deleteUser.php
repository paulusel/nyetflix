<?php

require_once "../includes.php";

try {
    validateRequest();
    $user = idetifyUser(false);

    Backend::deleteUser($user["user_id"]);
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
