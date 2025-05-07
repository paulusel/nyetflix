<?php

require_once "../helpers.php";
require_once "../backend/backend.php";

try {
    validateRequest();
    $user = idetifyUser();
    $lists = Backend::lists($user["user_id"]);
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
