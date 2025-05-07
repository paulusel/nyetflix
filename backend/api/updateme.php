<?php

require_once "../helpers.php";
require_once "../backend/backend.php";

try {
    validateRequest();
    $user_id = idetifyUser();
    $new_user = json_decode(file_get_contents("php://input"), true);

    if(!$new_user || !is_array($new_user)) {
        sendMessage("no user data found in the request body");
        exit;
    }

    Backend::updateme($user_id, $new_user);
    sendJson(["ok" => true]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e){
    sendMessage("internal server error", 500);
}
