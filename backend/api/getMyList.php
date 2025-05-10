<?php

require_once "../includes.php";

try {
    validateRequest();
    $profile = idetifyUser();

    $items = Backend::listItems($profile['user_id']);
    sendJson(['ok' => true, 'items' => $items]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e) {
    require '../logger.php';
    Logger::log($e->getMessage());
    sendMessage("internal server error", 500);
}
