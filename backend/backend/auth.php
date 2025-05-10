<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    private static string $priva_key_file = __DIR__ . '/private.key';
    private static string $public_key_file = __DIR__ . '/public.key';

    public static function newToken(array $user) : string {
        static $priv_key = file_get_contents(self::$priva_key_file);
        $payload = [
            'iat' => time(),
            'exp' => time() + 2592000,
            'user' => $user['user_id']
        ];

        if(isset($user['profile_id'])) {
            $payload['profile'] = $user['profile_id'];
        }

        return JWT::encode($payload, $priv_key, 'RS256');
    }

    public static function validate(string $token) : array {
        static $pub_key = new Key(file_get_contents(self::$public_key_file), 'RS256');
        try {
            $payload = (array)JWT::decode($token, $pub_key);
            $user = ['user_id' => $payload['user']];

            if(isset($payload['profile'])) {
                $user['profile_id'] = $payload['profile'];
            }

            return $user;
        }
        catch(UnexpectedValueException $e) {
            throw new BackendException('invalid authorization token', 401);
        }
    }
}
