<?php

require_once "../backend.php";
require_once "../helpers.php";

try {
    validateRequest();
    $user = idetifyUser();

    // generate home data
    // TODO: Generate movie lists

    sendJson(["ok" => true, "categories" => []]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e) {
    require '../logger.php';
    Logger::log($e->getMessage);
    sendMessage("internal server error", 500);
}

