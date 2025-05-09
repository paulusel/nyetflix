<?php

require_once "../includes.php";

try {
    validateRequest();
    $user = idetifyUser();

    $list_name = json_decode(file_get_contents("php://input"), true);
    if(!$list_name || !is_string($list_name)) {
        sendMessage("list name is not specified", 400);
        exit;
    }

    $list = Backend::ceateList($user['user_id'], $list_name);
    sendJson(["ok" => true, "list" => $list]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e){
    require '../logger.php';
    Logger::log($e->getMessage());
    sendMessage("internal server error", 500);
}
