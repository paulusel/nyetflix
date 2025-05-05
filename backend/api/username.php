<?php

require_once "../backend/backend.php";
require_once "../helpers.php";

try {
    $arr = json_decode(file_get_contents("php://input"), true);

    if(!isset($arr["username"]) || strlen($arr["username"]) === 0) {
        throw new BackendException("username not specified in request", 400);
    }

    $isAvailable = Backend::isUserNameAvailable($arr["username"]);
    sendJson(["ok" => true, "available" => $isAvailable]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e){
    sendMessage("internal server error", 500);
}
