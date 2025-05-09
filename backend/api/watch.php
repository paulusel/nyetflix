<?php

require_once '../includes.php';

try {
    validateRequest();
    $user = idetifyUser();

    $request = json_decode(file_get_contents('php://input'), true);

    if (!isset($request['movie_id'], $request['request_type'])) {
        throw new BackendException('video id is not specified in the request', 400);
    }

    // Sanitize video ID to prevent directory traversal
    $movie_id = preg_replace('/[^a-zA-Z0-9_-]/', '', $request["video_id"]);
    $movie_path = "media/movies/$movie_id";

    if ($request['request_type'] === 'manifest') {
        Backend::insertHistory($user['user_id'], $movie_id);
        servePlaylist($movie_path);
    } elseif ($request['request_type'] === 'segment') {
        if (!isset($request['segment_info']['sn'], $request['segment_info']['start'])) {
            throw new BackendException('missing segment information', 400);
        }
        Backend::updateHistory($user['useri_d'], $movie_id, $request['segment_info']['start']);
        serveSegment($movie_path, $request['segment_info']);
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

function servePlaylist($movie_path) {
    $playlist = "$movie_path/playlist.m3u8";
    if (!file_exists($playlist)) {
        throw new BackendException('Playlist not found', 404);
    }

    header('Content-Type: application/vnd.apple.mpegurl');
    readfile($playlist);
}

function serveSegment($movie_path, $segmentInfo) {
    $segmentNum = str_pad($segmentInfo['sn'], 5, '0', STR_PAD_LEFT);
    $segmentFile = "$movie_path/segment_$segmentNum.ts";

    if (!file_exists($segmentFile)) {
        throw new BackendException('video segment not found', 404);
    }
    header('Content-Type: video/MP2T');
    readfile($segmentFile);
}
