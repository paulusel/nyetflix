<?php

class BackendException extends Exception {}

class Backend
{
    private static $db_host = "localhost";
    private static $db_name = "nyetflix";
    private static $db_username = "nyetflix";
    private static $db_password = "nyetflix";

    public static function connection() : PDO {
        try {
            $db = new PDO("mysql:host=" . self::$db_host . ";dbname=" . self::$db_name, self::$db_username, self::$db_password);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $db;
        }
        catch(Exception $e) {
            error_log($e->getTraceAsString());
            throw new BackendException("internal server error", 500);
        }
    }

    public static function getMovie(string $movie_id) : array{
        try {
            $db = self::connection();
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
            error_log($e->getTraceAsString());
            throw new BackendException("internal server error", 500);
        }
    }

    public static function getLists(string $username) : array {
        try {
            $db = self::connection();
            $stmnt = $db->prepare("SELECT list_id, list_name FROM lists WHERE username = ?");
            $stmnt->execute([$username]);
            return $stmnt->fetchAll();
        }
        catch(BackendException $e) {
            throw $e;
        }
        catch(PDOException $e) {
            error_log($e->getTraceAsString());
            throw new BackendException("internal server error", 500);
        }
    }

    public static function addUser(array $user) : void {
        if(!isset($user->username, $user->password)) {
            throw new BackendException("invalid user data", 400);
        }

        // process $user and add to database
        try {

        }
        catch(Exception $e) {
            error_log($e->getTraceAsString());
            throw new BackendException("internal server error", 500);
        }
    }

    public static function isUsernameAvailable(string $username) : bool {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmnt->execute([$username]);
        $result = $stmnt->fetchAll();
        return empty($result);
    }

}
