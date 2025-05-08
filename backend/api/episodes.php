<?php

require_once '../backend/backend.php';
require_once '../helpers.php';

try {
    validateRequest();
    $user = idetifyUser();
    $season_info = json_decode(file_get_contents('php://input'), true);
    if(!isset($season_info['movie_id'], $season_info['season_id'])) {
        sendMessage("season information not specifified", 400);
        exit;
    }
    $episodes = Backend::getEpisodes($season_info['movie_id'], $season_info['season_id']);
    sendJson(['ok' => true,
            'movie_id' => $season_info['movie_id'],
            'season_id' => $season_info['season_id'],
            'seasons' => $episodes]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e){
    require '../logger.php';
    Logger::log($e->getMessage);
    sendMessage("internal server error", 500);
}

