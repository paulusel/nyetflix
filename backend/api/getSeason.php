<?php

require_once __DIR__ . '/../includes.php';

try {
    validateRequest();
    $profile = idetifyUser();

    $season_info = json_decode(file_get_contents('php://input'), true);
    if(!$season_info || !isset($season_info['movie_id'], $season_info['season_no'])) {
        sendMessage("season is not specifified", 400);
        exit;
    }
    $episodes = Backend::getSeasonByNumber($season_info['movie_id'], $season_info['season_no']);
    sendJson(['ok' => true, 'episodes' => $episodes]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e){
    require '../logger.php';
    Logger::log($e->getMessage());
    sendMessage("internal server error", 500);
}
