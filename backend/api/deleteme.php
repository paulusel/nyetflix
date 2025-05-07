<?php

require_once "../helpers.php";
require_once "../backend/backend.php";

try {
    validateRequest();
    $user = idetifyUser();
    Backend::deleteme($user["user_id"]);
    sendJson(["ok" => true]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e){
    sendMessage("internal server error", 500);
}
