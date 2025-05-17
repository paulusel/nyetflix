<?php

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

    public static function getMovieDetail(string $movie_id) : array{
        $db = self::connection();
        $stmnt = $db->prepare("SELECT movie_id, title, description, type, ext FROM movies WHERE movie_id = ?");
        $stmnt->execute([$movie_id]);
        $movie = $stmnt->fetch();
        if(!$movie) {
            throw new BackendException("movie not found", 404);
        }
        return $movie;
    }

    /**
     * @param array $user: new user information. Must contain 'email' and password
     * @return array: user information after addition of user_id to it
     */
    public static function subscribe(array $user) : array {
        if(!isset($user["email"], $user["password"], $user['name'])) {
            throw new BackendException("missing password, email or name field", 400);
        }

        if(!is_string($user['email']) || !is_string($user['password']) || !is_string($user['name'])) {
            throw new BackendException('invalid user data types', 400);
        }

        if(strlen($user['password']) < 4) {
            throw new BackendException('password too short', 400);
        }

        try {
            $db = self::connection();
            $password_hash = password_hash($user['password'], PASSWORD_DEFAULT);
            $stmnt = $db->prepare('INSERT INTO users (email, password, name) VALUES (?, ?, ?)');
            $stmnt->execute([$user['email'], $password_hash, $user['name']]);
            unset($user['password']);

            // get user_id
            $stmnt = $db->prepare("SELECT LAST_INSERT_ID() AS id");
            $stmnt->execute();
            $user["user_id"] = $stmnt->fetch()["id"];


            $stmnt = $db->prepare("INSERT INTO profiles (user_id, name) VALUES (?, ?)");
            $stmnt->execute([$user['user_id'], $user['name']]);

            return $user;
        }
        catch(PDOException $e) {
            if($e->errorInfo[1] === 1062) {
                throw new BackendException("email already registered with another user", 400);
            }
            else {
                throw $e;
            }
        }
    }

    public static function getUser(int $user_id) : array {
        $db = self::connection();
        $user = self::getUserData($user_id, $db);
        if(!$user) {
            throw new BackendException("user not found", 404);
        }

        return (array)$user;
    }

    /**
     * @param array $user: new user information. Must contain 'email' and password
     */
    public static function authenticate(array $user) : array {
        if(!isset($user['email'], $user['password'])) {
            throw new BackendException("invalid user data", 400);
        }

        $password_hash = password_hash($user["password"], PASSWORD_DEFAULT);

        $db = self::connection();
        $stmnt = $db->prepare("SELECT user_id, name, email, password FROM users WHERE email = ?");
        $stmnt->execute([$user["email"]]);
        $stored_user = $stmnt->fetch();

        if(!$stored_user || !password_verify($user['password'], $stored_user['password'])) {
            throw new BackendException("incorrect email or password", 400);
        }

        unset($stored_user['password']);
        return $stored_user;
    }

    /**
     * Check whether associated with a user_id is valid
     * @param int $user_id: user id to be checked
     * @return bool: true if user exists, false otherwise
     */
    private static function getUserData(int $user_id, PDO $db) : array|bool {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT user_id, name, email FROM users WHERE user_id = ?");
        $stmnt->execute([$user_id]);
        return $stmnt->fetch();
    }

    public static function addProfile(array $profile) : array {
        if(!isset($profile['name'], $profile['user_id']) || strlen($profile['name']) === 0) {
            throw new BackendException('invalid profile data. required fields missing or empty', 400);
        }

        $db = self::connection();
        $stmnt = $db->prepare("INSERT INTO profiles (user_id, name, picture) VALUES (?, ?, ?)");
        $stmnt->execute([$profile['user_id'], $profile['name'], $profile['picture'] ?? null]);

        $profile['profile_id'] = $db->lastInsertId();
        return $profile;
    }

    public static function getProfile(int $profile_id) : array {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT user_id, profile_id, name, picture FROM profiles WHERE profile_id = ?");
        $stmnt->execute([$profile_id]);
        $profile = $stmnt->fetch();
        if(!$profile) {
            throw new BackendException('profile not found', 404);
        }

        return $profile;
    }

    public static function getUserProfiles(int $user_id) : array {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT user_id, profile_id, name, picture FROM profiles WHERE user_id = ?");
        $stmnt->execute([$user_id]);
        return $stmnt->fetchAll();
    }

    /**
     *  Update user information.
     *
     * @param array $profile data to replace existing user information
     * @param int $profile_id whose information is to be updated
     */
    public static function updateProfile(int $user_id, array $profile) : void {
        $updates = [];
        $values = [];

        $db = self::connection();

        if(isset($profile['name'])) {
            $updates[] = ' name = ? ';
            $values[] = $profile['name'];
        }

        if(isset($profile['picture'])) {
            $updates[] = ' picture = ? ';
            $values[] = $profile['picture'];
        }

        if(empty($updates) || !isset($profile['profile_id'])) {
            throw new BackendException("required fields missing or update is empty", 400);
        }

        $values[] = $profile['profile_id'];
        $values[] = $user_id;

        $stmnt = $db->prepare( "UPDATE profiles SET " . implode(", ", $updates) . " WHERE profile_id = ? AND user_id = ?");
        $stmnt->execute($values);

        if(0 === $stmnt->rowCount()) {
            throw new BackendException('profile not found', 404);
        }
    }

    public static function changePassword(int $user_id, string $password) : void {
        if(strlen($password) < 4) {
            throw new BackendException('password too short', 400);
        }
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $db = self::connection();
        $stmnt = $db->prepare('UPDATE users SET password = ? WHERE user_id = ?');
        $stmnt->execute([$password_hash, $user_id]);
    }

    public static function deleteUser(int $user_id) : void {
        $db = self::connection();
        $stmnt = $db->prepare("DELETE FROM users WHERE user_id = ?");
        $stmnt->execute([$user_id]);
    }

    public static function deleteProfile(int $user_id, int $profile_id) : void {
        $db = self::connection();
        $stmnt = $db->prepare("DELETE FROM profiles WHERE user_id = ? AND profile_id = ?");
        $stmnt->execute([$user_id, $profile_id]);
        if(0 === $stmnt->rowCount()) {
            throw new BackendException('profile not found', 404);
        }
    }

    public static function listItems(int $profile_id) : array {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT movies.movie_id, type, ext FROM lists JOIN "
            . " movies ON lists.movie_id = movies.movie_id WHERE profile_id = ?");
        $stmnt->execute([$profile_id]);
        return $stmnt->fetchAll();
    }

    public static function addToList(int $profile_id, int $movie_id) : void {
        $db = self::connection();
        $stmnt = $db->prepare("INSERT INTO lists (profile_id, movie_id) VALUES (?, ?)");
        $stmnt->execute([$profile_id, $movie_id]);
    }

    public static function removeFromList(int $profile_id, int $movie_id) : void {
        $db = self::connection();
        $stmnt = $db->prepare("DELETE FROM lists WHERE profile_id = ? AND movie_id = ?");
        $stmnt->execute([$profile_id, $movie_id]);
        if(0 === $stmnt->rowCount()) {
            throw new BackendException('movie not found in the list', 404);
        }
    }

    public function verifyUser(string $email, string $password) : array {
        $stmt = $this->db->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!password_verify($password, $user['password'])) {
            throw new BackendException("Invalid credentials", 401);
        }

        return [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ];
    }

    public static function insertHistory(int $profile_id, int $movie_id) : void {
        $db = self::connection();
        $stmnt = $db->prepare("INSERT INTO history (profile_id, movie_id) VALUES (?, ?)");
        $stmnt->execute([$profile_id, $movie_id]);
    }

    public static function updateHistory(int $profile_id, int $movie_id, float $position) : void {
        // user_id, movie_id, position
        $db = self::connection();
        $stmnt = $db->prepare("UPDATE history SET position = ? WHERE profile_id = ? AND movie_id = ?");
        $stmnt->execute([$position, $profile_id, $movie_id]);
    }

    public static function getFilmsSeries(int $type) : array {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT movie_id, type, ext FROM movies WHERE type = ?");
        $stmnt->execute([$type]);
        return $stmnt->fetchAll();
    }

    public static function getSeasonByNumber(int $movie_id, int $season_no) : array {
        $db = self::connection();
        $full_seasons = [];
        $stmnt = $db->prepare("SELECT season_id FROM seasons WHERE movie_id = ? AND season_no = ?");
        $stmnt->execute([$movie_id, $season_no]);
        $season = $stmnt->fetch();

        if(!$season) {
            throw new BackendException('season not found', 404);
        }

        return self::getSeasonById($season['season_id']);
    }

    public static function getSeasonById(int $season_id) : array {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT episode_no, movies.movie_id, title, description, type, ext " .
            " FROM episodes JOIN movies ON episodes.movie_id = movies.movie_id WHERE season_id = ?");
        $stmnt->execute([$season_id]);
        return $stmnt->fetchAll();
    }

    public static function getGenreMovies(string $genre) : array {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT movies.movie_id, type, ext FROM movies JOIN movie_genres " .
            " ON movies.movie_id = movie_genres.movie_id WHERE genre = ?");
        $stmnt->execute([$genre]);
        return $stmnt->fetchAll();
    }

    public static function getHistory(int $profile_id) : array {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT movies.movie_id, type, ext FROM movies JOIN history " .
                " ON movies.movie_id = history.movie_id WHERE profile_id = ?");
        $stmnt->execute([$profile_id]);
        return $stmnt->fetchAll();
    }

    public static function getRecents() : array {
        $db = self::connection();
        $stmnt = $db->prepare("SELECT movie_id, type, ext FROM movies WHERE type IN (1, 2)" . 
            "ORDER BY added DESC LIMIT 100");
        $stmnt->execute([]);
        return $stmnt->fetchAll();
    }

}
