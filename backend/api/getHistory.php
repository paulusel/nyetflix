<?php

require_once __DIR__ . '/../includes.php';

try {
    validateRequest(false);
    $profile = idetifyUser();

    $history = Backend::getHistory($profile['profile_id']);
    sendJson(['ok' => true, 'history' => $history]);
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
}
catch(Throwable $e){
    require __DIR__ . '/../logger.php';
    Logger::log($e->getMessage());
    sendMessage("internal server error", 500);
}
