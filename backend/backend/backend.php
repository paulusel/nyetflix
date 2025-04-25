<?php

class BackendException extends Exception {}

class Backend {

    private static $db_host = "localhost";
    private static $db_name = "nyetflix";
    private static $db_username = "nyetflix";
    private static $db_password = "";
    private static $db = null;

    public function __construct() {
        try {
            $this->db = new PDO("mysql:host=$this->db_host;dbname=$this->db_name", $this->db_username, $this->db_password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(Exception $e) {
            // TODO: write server log
            throw new BackendException("Internal Server Error");
        }
    }

    public function get_movie(string $movie_id) : array{
        return []; // to be removed
    }

    public function get_lists(string $username) : array {
        return []; // placeholder
    }
}
