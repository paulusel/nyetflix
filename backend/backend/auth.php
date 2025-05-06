<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use UnexpectedValueException;

require_once 'backend.php';

class Auth {
    public static function newToken(int $user_id) : string {
        static $priv_key = file_get_contents('private.key');
        $payload = [
            'iat' => time(),
            'exp' => time() + 2592000,
            'sub' => $user_id
        ];

        return JWT::encode($payload, $priv_key, 'RS256');
    }

    public static function validate(string $token) : int {
        static $pub_key = new Key(file_get_contents('public.key'), 'RS256');
        try {
            $payload = (array)JWT::decode($token, $pub_key);
            return $payload["sub"];
        }
        catch(UnexpectedValueException $e) {
            throw new BackendException('invalid authorization token', 401);
        }
    }
}
