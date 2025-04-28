<?php

require_once "../backend.php";
require_once "../helpers.php";

try {
    validateRequest();
    idetifyUser();

    $arr = json_decode(file_get_contents("php://input"), true);
    if(!isset($arr) || !isset($arr["movie_id"])) {
        throw new BackendException("username not specified in request", 400);
    }

    $movie = Backend::movie($arr["movie_id"]);
    sendJson($movie);
}
catch(BackendException $e) {
    echo $e->getMessage();
    exit;
}
