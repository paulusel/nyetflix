<?php

require_once '../backend/backend.php';
require_once '../helpers.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: POST');

try {
    validateRequest();
    $user = idetifyUser();

    $request = json_decode(file_get_contents('php://input'), true);

    if (!isset($request['video_id'], $request['request_type'])) {
        throw new BackendException('video id is not specified in the request', 400);
    }

    // Sanitize video ID to prevent directory traversal
    $movieId = preg_replace('/[^a-zA-Z0-9_-]/', '', $request["video_id"]);
    $moviePath = 'media/movies/$movieId';

    if ($request['request_type'] === 'manifest') {
        Backend::insertHistory($user['user_id'], $movieId);
        servePlaylist($moviePath);
    } elseif ($request['request_type'] === 'segment') {
        if (!isset($request['segment_info']['sn'], $request['segment_info']['start'])) {
            throw new BackendException('missing segment information', 400);
        }
        Backend::updateHistory($user['useri_d'], $movieId, $request['segment_info']['start']);
        serveSegment($moviePath, $request['segment_info']);
    } else {
        throw new BackendException('invalid request type', 400);
    }
}
catch(BackendException $e) {
    sendMessage($e->getMessage(), $e->getCode());
} catch (Throwable $e) {
    require_once '../logger.php';
    Logger::log($e->getMessage() . PHP_EOL);
    sendMessage("internal server error", 500);
}

function servePlaylist($moviePath) {
    $playlist = "$moviePath/playlist.m3u8";
    if (!file_exists($playlist)) {
        throw new BackendException('Playlist not found', 404);
    }

    header('Content-Type: application/vnd.apple.mpegurl');
    readfile($playlist);
}

function serveSegment($moviePath, $segmentInfo) {
    $segmentNum = str_pad($segmentInfo['sn'], 5, '0', STR_PAD_LEFT);
    $segmentFile = "$moviePath/segment_$segmentNum.ts";

    if (!file_exists($segmentFile)) {
        throw new BackendException('video segment not found', 404);
    }
    header('Content-Type: video/MP2T');
    readfile($segmentFile);
}
