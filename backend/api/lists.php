<?php

require_once "../includes.php";

try {
    validateRequest();
    $user = idetifyUser();
    $lists = Backend::getLists($user["user_id"]);
    sendJson($lists);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e) {
    require '../logger.php';
    Logger::log($e->getMessage);
    sendMessage("internal server error", 500);
}
