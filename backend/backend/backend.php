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
            Logger::log($e->getTraceAsString(),"ERROR");
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
            Logger::log($e->getTraceAsString(),"ERROR");
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
            Logger::log($e->getTraceAsString(),"ERROR");
            throw new BackendException("internal server error", 500);
        }
    }

    public function addUser(string $username, string $password, string $role) : void {
        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->rowCount() > 0) {
                throw new BackendException("Username already exists", 409);
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $this->db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashedPassword, $role]);
        } catch (BackendException $e) {
            throw $e;
        } catch (Exception $e) {
            Logger::log("BackendException: " .$e->getMessage(),"ERROR");
            throw new BackendException("Internal server error", 500);
        }
    }
    public function verifyUser(string $username, string $password) : array {
        try {
            $stmt = $this->db->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password'])) {
                return [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ];
            }

            throw new BackendException("Invalid credentials", 401);
        } catch (BackendException $e) {
            throw $e;
        } catch (Exception $e) {
            Logger::log("BackendException: ".$e->getMessage(),"ERROR");
            throw new BackendException("Internal server error", 500);
        }
    }

}
