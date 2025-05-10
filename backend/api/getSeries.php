<?php

require_once __DIR__ . '../includes.php';

try {
    validateRequest();
    $profile = idetifyUser();

    $series = Backend::getFilmsSeries(2);
    sendJson(['ok' => true, 'series' => $series]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e){
    require '../logger.php';
    Logger::log($e->getMessage());
    sendMessage("internal server error", 500);
}

