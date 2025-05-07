<?php

require_once "../helpers.php";
require_once "../backend/backend.php";
require_once "../backend/auth.php";

try {
    $user = json_decode(file_get_contents("php://input"), true);

    if(!$user || !is_array($user)) {
        sendMessage("no login data found in the request body", 400);
        exit;
    }

    $user = Backend::signup($user);
    $token = Auth::newToken($user["user_id"]);
    sendJson(["ok" => true, "token" => $token, "user" => $user]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e){
    sendMessage("internal server error", 500);
}
