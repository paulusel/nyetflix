<?php

require_once "../includes.php";

try {
    validateRequest();
    $user = idetifyUser(false);

    $profiles = Backend::getUserProfiles($user["user_id"]);
    sendJson(['ok' => true, 'items' => $profiles]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e) {
    require '../logger.php';
    Logger::log($e->getMessage());
    sendMessage("internal server error", 500);
}
