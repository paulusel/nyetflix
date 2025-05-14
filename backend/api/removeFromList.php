<?php

require_once __DIR__ . '/../includes.php';

try {
    validateRequest();
    $profile = idetifyUser();

    $movie_id = json_decode(file_get_contents("php://input"), true);

    if(!$movie_id || !is_int($movie_id)) {
        sendMessage("no movie id specified", 400);
        exit;
    }
    Backend::removeFromList($profile['profile_id'], $movie_id);
    sendJson(['ok' => true]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e){
    require __DIR__ . '/../logger.php';
    Logger::log($e->getMessage());
    sendMessage("internal server error", 500);
}
