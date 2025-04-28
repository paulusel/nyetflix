<?php

class BackendException extends Exception {}

class Backend
{
    private static $db_host = "localhost";
    private static $db_name = "nyetflix";
    private static $db_username = "nyetflix";
    private static $db_password = "nyetflix";

    private static function connection() : PDO {
        $db = new PDO("mysql:host=" . self::$db_host . ";dbname=" . self::$db_name, self::$db_username, self::$db_password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    }

    public static function movie(string $movie_id) : array{
        $db = self::connection();
        $stmnt = $db->prepare("SELECT movie_id, title, description, thumbnail, video, FROM lists WHERE username = ?");
        $stmnt->execute([$movie_id]);
        $result = $stmnt->fetchAll();
        if(empty($result)) {
            throw new BackendException("movie not found", 404);
        }
        return $result[0];
    }

    public static function lists(int $user_id) : array {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT list_id, list_name FROM lists WHERE user_id = ?");
        $stmnt->execute([$user_id]);
        $lists = $stmnt->fetchAll();

        foreach($lists as $list) {
            $stmnt = $db->prepare("SELECT movie_id, movie_id, title, description, thumbnail, video
                FROM user_lists JOIN movies ON user_lists.movie_id = movies.movie_id WHERE list_id = ?");
            $stmnt->execute([$list["list_id"]]);
            $list["movies"] = $stmnt->fetchAll();
        }

        return $lists;
    }

    public static function signup(array $user) : array {
        $db = self::connection();
        if(!isset($user["username"], $user["password"])) {
            throw new BackendException("missing password or username field", 400);
        }

        if(!self::isUserNameAvailable($user["username"], $db)) {
            throw new BackendException("username already taken", 400);
        }

        // process $user and add to database
        $password_hash = password_hash($user["password"], PASSWORD_DEFAULT);
        $stmnt = $db->prepare("INSERT into users (username, password) VALUES (?, ?)");
        $stmnt->execute([$user["username"], $password_hash]);

        // get user_id
        $stmnt = $db->prepare("SELECT LAST_INSERT_ID() AS id");
        $stmnt->execute();
        $user["user_id"] = $stmnt->fetchAll()["id"];

        return $user;
    }

    public static function seasons(int $movie_id, PDO $db) : array {
        $full_seasons = [];

        $db = $db ? $db : self::connection();
        $stmnt = $db->prepare("SELECT season_id, season_no, title, description FROM seasons WHERE movie_id = ?");
        $stmnt->execute([$movie_id]);

        foreach($stmnt->fetchAll() as $season ) {
            $stmnt = $db->prepare("SELECT movie_id, title, description, thumbnail, video " .
                " FROM episodes JOIN movies ON episodes.movie_id = movies.movie_id WHERE season_id = ?");
            $stmnt->execute([$season["season_id"]]);
            $episodes = $stmnt->fetchAll();

            $season["episodes"] = $episodes;
            $full_seasons[] = $season;
        }

        return $full_seasons;
    }

    public static function signin(array $user) : array {
        if(!isset($user->username, $user->password)) {
            throw new BackendException("invalid user data", 400);
        }

        $password_hash = password_hash($user["password"], PASSWORD_DEFAULT);

        $db = self::connection();
        $stmnt = $db->prepare("SELECT user_id, username, role, picture FROM users WHERE username = ? AND password = ?");
        $stmnt->execute([$user["username"], $password_hash]);
        $result = $stmnt->fetchAll();

        if(empty($result)) {
            throw new BackendException("invalid credentials", 400);
        }

        return $result[0];
    }

    public static function isUserNameAvailable(string $username, PDO | null $db = null) : bool {
        if(is_null($db)) {
            $db = self::connection();
        }
        $stmnt = $db->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmnt->execute([$username]);
        $result = $stmnt->fetchAll();
        return empty($result);
    }

    public static function checkUser(int $user_id, PDO | null $db = null) : bool {
        if(is_null($db)) {
            $db = self::connection();
        }
        $stmnt = $db->prepare("SELECT username FROM users WHERE user_id = ?");
        $stmnt->execute([$user_id]);
        $result = $stmnt->fetchAll();
        return !empty($result);
    }

    public static function updateme(int $user_id, array $user) : void {
        $updates = [];
        $values = [];

        $db = self::connection();

        if(isset($user["username"])) {
            if(!self::isUserNameAvailable($user["username"], $db)) {
                throw new BackendException("username already taken", 400);
            }

            $updates[] = " username = ? ";
            $values[] = $user["username"];
        }

        if(isset($user["picture"])) {
            $updates[] = " picture = ? ";
            $values[] = $user["picture"];
        }

        if(empty($updates) || !isset($user["user_id"])) {
            throw new BackendException("empty update", 400);
        }

        $values[] = $user_id;
        $stmnt = $db->prepare( "UPDATE users SET " . implode(", ", $updates) . " WHERE user_id = ?");
        $stmnt->execute($values);
    }

    public static function deleteme(int $user_id) : void {
        $db = self::connection();
        if(!self::checkUser($user_id, $db)) {
            throw new BackendException("user not found", 404);
        }

        $stmnt = $db->prepare("DELETE FROM users WHERE users_id = ?");
        $stmnt->execute([$user_id]);
    }

    public static function getme(int $user_id) : array {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT user_id, username, role, picture FROM users WHERE user_id = ?");
        $stmnt->execute([$user_id]);
        $rows = $stmnt->fetchAll();
        if(empty($rows)) {
            throw new BackendException("user not found", 404);
        }

        return $rows[0];
    }

    public static function home(int $user_id) : array {
        // TODO: stub
        return [];
    }
}
