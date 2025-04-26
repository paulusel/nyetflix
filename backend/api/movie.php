<?php

if($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(400);
    exit;
}

include "../backend.php";

try {
    $backend = new Backend();
    $movies = $backend->get_movie("game_of_thrones");
    // Do something with the list
}
catch(BackendException $e) {
    echo $e->getMessage();
    exit;
}
