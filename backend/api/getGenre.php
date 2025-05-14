<?php

require_once __DIR__ . '/../includes.php';

try {
    validateRequest();
    $profile = idetifyUser();

    $genre = json_decode(file_get_contents("php://input"), true);
    if(!$genre || !is_string($genre)) {
        sendMessage('no genre specified', 400);
        exit;
    }

    $genre_movies = Backend::getGenreMovies($genre);
    sendJson(['ok' => true, 'movies' => $genre_movies]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e){
    require __DIR__ . '/../logger.php';
    Logger::log($e->getMessage());
    sendMessage("internal server error", 500);
}
