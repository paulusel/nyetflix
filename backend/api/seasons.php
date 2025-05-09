<?php

require_once __DIR__ . '../includes.php';

try {
    validateRequest();
    $user = idetifyUser();
    $movie_id = json_decode(file_get_contents('php://input'), true);
    if(!$movie_id || is_numeric($movie_id)) {
        sendMessage("movie_id not specifified", 400);
        exit;
    }
    $seasons = Backend::getSeasons($movie_id);
    sendJson(['ok' => true, 'seasons' => $seasons]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e){
    require '../logger.php';
    Logger::log($e->getMessage);
    sendMessage("internal server error", 500);
}
