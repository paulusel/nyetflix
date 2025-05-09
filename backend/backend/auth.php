<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    private static string $priva_key_file = __DIR__ . '/private.key';
    private static string $public_key_file = __DIR__ . '/public.key';

    public static function newToken(int $user_id) : string {
        static $priv_key = file_get_contents(self::$priva_key_file);
        $payload = [
            'iat' => time(),
            'exp' => time() + 2592000,
            'sub' => $user_id
        ];

        return JWT::encode($payload, $priv_key, 'RS256');
    }

    public static function validate(string $token) : int {
        static $pub_key = new Key(file_get_contents(self::$public_key_file), 'RS256');
        try {
            $payload = (array)JWT::decode($token, $pub_key);
            return $payload["sub"];
        }
        catch(UnexpectedValueException $e) {
            throw new BackendException('invalid authorization token', 401);
        }
    }
}
