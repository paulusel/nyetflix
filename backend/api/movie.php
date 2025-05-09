<?php

require_once "../includes.php";

try {
    validateRequest();
    idetifyUser();
    $movie_id = json_decode(file_get_contents("php://input"), true);

    if(!$movie_id || !is_string($movie_id)) {
        sendMessage("no movie id specified in request", 400);
        exit;
    }

    $movie = Backend::getMovie($movie_id);
    sendJson(["ok" => true, "movie" => $movie]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e) {
    require '../logger.php';
    Logger::log($e->getMessage());
    sendMessage("internal server error", 500);
}
