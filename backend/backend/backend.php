<?php

require_once '../logger.php';

class BackendException extends Exception {}

class Backend {
    private static $db_host = "localhost";
    private static $db_name = "nyetflix";
    private static $db_username = "nyetflix";
    private static $db_password = "nyetflix";

    private static function connection() : PDO {
        $db = new PDO("mysql:host=" . self::$db_host . ";dbname=" . self::$db_name, self::$db_username, self::$db_password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $db;
    }

    public static function getMovie(string $movie_id) : array{
        $db = self::connection();
        $stmnt = $db->prepare("SELECT movie_id, title, description, thumbnail, video, FROM movies WHERE movie_id = ?");
        $stmnt->execute([$movie_id]);
        $movie = $stmnt->fetch();
        if(!$movie) {
            throw new BackendException("movie not found", 404);
        }
        return $movie;
    }

    public static function getLists(int $user_id) : array {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT list_id, list_name FROM lists WHERE user_id = ?");
        $stmnt->execute([$user_id]);
        $lists = $stmnt->fetchAll();

        $stmnt = $db->prepare("SELECT movie_id, movie_id, title, description, thumbnail, video
            FROM user_lists JOIN movies ON user_lists.movie_id = movies.movie_id WHERE list_id = ?");

        foreach($lists as $list) {
            $stmnt->execute([$list["list_id"]]);
            $list["movies"] = $stmnt->fetchAll();
        }

        return $lists;
    }

    /**
     * @param array $user: new user information. Must contain 'username' and password
     * @return array: user information after addition of user_id to it
     */
    public static function signup(array $user) : array {
        if(!isset($user->username, $user->password)) {
            throw new BackendException("invalid user data", 400);
        }

        if(!isset($user["username"], $user["password"])) {
            throw new BackendException("missing password or username field", 400);
        }

        if(!self::isUserNameAvailable($user["username"])) {
            throw new BackendException("username already taken", 400);
        }

        $db = self::connection();

        // process $user and add to database
        $password_hash = password_hash($user["password"], PASSWORD_DEFAULT);
        $stmnt = $db->prepare("INSERT into users (username, password) VALUES (?, ?)");
        $stmnt->execute([$user["username"], $password_hash]);

        // get user_id
        $stmnt = $db->prepare("SELECT LAST_INSERT_ID() AS id");
        $stmnt->execute();
        $user["user_id"] = $stmnt->fetch()["id"];

        return $user;
    }

    /**
     * @param array $user: new user information. Must contain 'username' and password
     * @return array: same user information after missing fields are filled in.
     */
    public static function signin(array $user) : array {
        if(!isset($user->username, $user->password)) {
            throw new BackendException("invalid user data", 400);
        }

        $password_hash = password_hash($user["password"], PASSWORD_DEFAULT);

        $db = self::connection();
        $stmnt = $db->prepare("SELECT user_id, username, role, picture FROM users WHERE username = ? AND password = ?");
        $stmnt->execute([$user["username"], $password_hash]);
        $user = $stmnt->fetch();

        if(!$user) {
            throw new BackendException("invalid credentials", 400);
        }

        return $user;
    }

    public static function isUserNameAvailable(string $username) : bool {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmnt->execute([$username]);
        $result = $stmnt->fetchAll();
        return empty($result);
    }

    /**
     * Check whether associated with a user_id is valid
     * @param int $user_id: user id to be checked
     * @return bool: true if user exists, false otherwise
     */
    private static function checkUser(int $user_id) : bool {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT username FROM users WHERE user_id = ?");
        $stmnt->execute([$user_id]);
        return !$stmnt->fetch();
    }

    /**
     *  Update user information.
     *
     * @param array $user data to replace existing user information
     * @param int $user_id user_id whose information is to be updated
     */
    public static function updateMe(int $user_id, array $user) : void {
        $updates = [];
        $values = [];

        $db = self::connection();

        if(isset($user["password"])) {
            $updates[] = " password = ? ";
            $password_hash = password_hash($user["password"], PASSWORD_DEFAULT);
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

    public static function deleteMe(int $user_id) : void {
        $db = self::connection();
        if(!self::checkUser($user_id)) {
            throw new BackendException("user not found", 404);
        }

        $stmnt = $db->prepare("DELETE FROM users WHERE users_id = ?");
        $stmnt->execute([$user_id]);
    }

    public static function getMe(int $user_id) : array {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT user_id, username, role, picture FROM users WHERE user_id = ?");
        $stmnt->execute([$user_id]);
        $user = $stmnt->fetch();
        if(!$user) {
            throw new BackendException("user not found", 404);
        }
        return $user;
    }

    public function verifyUser(string $username, string $password) : array {
        $stmt = $this->db->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!password_verify($password, $user['password'])) {
            throw new BackendException("Invalid credentials", 401);
        }

        return [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];
    }

    public static function getHome(int $user_id) : array {
        // TODO: stub
        return [];
    }

    public static function insertHistory(int $user_id, int $movie_id) : void {
        $db = self::connection();
        $stmnt = $db->prepare("INSERT INTO history (user_id, movie_id) VALUES (?, ?)");
        $stmnt->execute([$user_id, $movie_id]);
    }

    public static function updateHistory(int $user_id, int $movie_id, float $position) : void {
        // user_id, movie_id, position
        $db = self::connection();
        $stmnt = $db->prepare("UPDATE history SET position = ? WHERE user_id = ? AND movie_id = ?");
        $stmnt->execute([$position, $user_id, $movie_id]);
    }

    public static function getSeries() : array {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT movie_id, title, description, thumbnail FROM movies WHERE video IS NULL");
        $stmnt->execute();
        return $stmnt->fetchAll();
    }

    public static function getSeasons(int $movie_id) : array {
        $db = self::connection();
        $full_seasons = [];
        $stmnt = $db->prepare("SELECT season_id, season_no, title, description FROM seasons WHERE movie_id = ?");
        $stmnt->execute([$movie_id]);
        $seasons = $stmnt->fetchAll();

        $stmnt = $db->prepare("SELECT episode_no, movie_id, title, description, thumbnail, video " .
            " FROM episodes JOIN movies ON episodes.movie_id = movies.movie_id WHERE season_id = ? AND movie_id = ?");

        foreach($seasons as $season ) {
            $stmnt->execute([$season["season_id"], $movie_id]);
            $episodes = $stmnt->fetchAll();

            $season["episodes"] = $episodes;
            $full_seasons[] = $season;
        }

        return $full_seasons;
    }

    public static function getEpisodes(int $movie_id, int $season_id) : array {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT movie_id, title, description, thumbnail, video " .
            " FROM episodes JOIN movies ON episodes.movie_id = movies.movie_id WHERE season_id = ? AND movie_id = ?");
        $stmnt->execute([$season_id, $movie_id]);
        return $stmnt->fetchAll();
    }
}
