<?php

require_once "../helpers.php";
require_once "../backend/backend.php";

try {
    $user = json_decode(file_get_contents("php://input"), true);
    if(!is_null($user)) {
        throw new BackendException("empty request body", 400);
    }
    $user = Backend::signup($user);
    $token = Auth::newToken($user["user_id"]);
    sendJson(["ok" => true, "token" => $token, "user" => $user]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Exception $e){
    sendMessage("internal server error", 500);
}
