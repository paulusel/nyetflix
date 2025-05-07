<?php

require_once "../backend.php";
require_once "../helpers.php";

try {
    validateRequest();
    idetifyUser();
    $movie_id = json_decode(file_get_contents("php://input"), true);

    if(!$movie_id || !is_string($movie_id)) {
        sendMessage("no movie_id specified in request", 400);
        exit;
    }

    $movie = Backend::movie($movie_id);
    sendJson($movie);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e) {
    sendMessage("internal server error", 500);
}
