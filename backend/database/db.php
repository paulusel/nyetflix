<?php

class Connection {

private static $db_host = "localhost";
private static $db_name = "nyetflix";
private static $db_username = "nyetflix";
private static $db_password = "";

    function __construct() {
        try {
            $db = new PDO("mysql:host=$this->db_host;dbname=$this->db_name", $this->db_username, $this->db_password);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e) {
            http_response_code(500);
            // TODO: write server log
            echo "Internal Server Error";
            exit;
        }
    }
}
