<?php

$db_host = "localhost";
$db_name = "nyetflix";
$db_username = "nyetflix";
$db_password = "";

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
    http_response_code(500);
    // TODO: write server log
    echo "Internal Server Error";
    exit;
}
