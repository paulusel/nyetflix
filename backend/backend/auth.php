<?php

class Auth {
    public static function newToken(array $user) : string {
        return "djdk9389dioioirfg";
    }

    public static function validate(string $token) : array {
        return ["username" => "user", "role" => "user"];
    }
}
