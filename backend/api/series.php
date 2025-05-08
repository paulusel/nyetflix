<?php

require_once '../backend/backend.php';
require_once '../helpers.php';

try {
    $user = idetifyUser();
    $series = Backend::getSeries();
    sendJson(['ok' => true, 'series' => $series]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e){
    require '../logger.php';
    Logger::log($e->getMessage);
    sendMessage("internal server error", 500);
}

