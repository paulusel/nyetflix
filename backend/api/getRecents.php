<?php

require_once __DIR__ . '/../includes.php';

try {
    validateRequest(false);
    $profile = idetifyUser();

    $recent_movies = Backend::getRecents();
    sendJson(['ok' => true, 'movies' => $recent_movies]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e){
    require __DIR__ . '/../logger.php';
    Logger::log($e->getMessage());
    sendMessage("internal server error", 500);
}
