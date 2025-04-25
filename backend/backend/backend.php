<?php

include "../logger.php";

class BackendException extends Exception {}

class Backend
{
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
            Logger::log($e->getTraceAsString());
            throw new BackendException("internal server error", 500);
        }
    }

    public function getMovie(string $movie_id) : array{
        try {
            $db = $this->db;
            $stmnt = $db->prepare("SELECT movie_id, title, description, thumbnail, video, FROM lists WHERE username = ?");
            $stmnt->execute([$movie_id]);
            $result = $stmnt->fetchAll();
            if(empty($result)) {
                throw new BackendException("movie not found", 404);
            }
            return $result;
        }
        catch(BackendException $e) {
            throw $e;
        }
        catch(Exception $e) {
            Logger::log($e->getTraceAsString());
            throw new BackendException("internal server error", 500);
        }
    }

    public function getLists(string $username) : array {
        try {
            $db = $this->db;
            $stmnt = $db->prepare("SELECT list_id, list_name FROM lists WHERE username = ?");
            $stmnt->execute([$username]);
            return $stmnt->fetchAll();
        }
        catch(BackendException $e) {
            throw $e;
        }
        catch(PDOException $e) {
            Logger::log($e->getTraceAsString());
            throw new BackendException("internal server error", 500);
        }
    }

    public function addUser(array $user) : void {
        if(!isset($user->username, $user->password)) {
            throw new BackendException("invalid user data", 400);
        }

        // process $user and add to database
        try {

        }
        catch(Exception $e) {
            Logger::log($e->getTraceAsString());
            throw new BackendException("internal server error", 500);
        }
    }
}
