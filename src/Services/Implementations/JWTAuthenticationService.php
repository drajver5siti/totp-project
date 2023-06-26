<?php

declare(strict_types=1);

namespace App\Services\Implementations;

use App\Services\AuthenticationServiceInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuthenticationService implements AuthenticationServiceInterface
{
    private const JWT_SECRET_KEY = 'JWT_SECRET';
    private const JWT_COOKIE_KEY = 'JWT_TOKEN';
    private const ALGO = 'HS256';
    
    public function getUsername(): string
    {
        $res = (array) JWT::decode($_COOKIE[self::JWT_COOKIE_KEY], new Key($_ENV[self::JWT_SECRET_KEY], self::ALGO));
        return $res['username'];
    }

    public function isLoggedIn(): bool
    {
        if(!isset($_COOKIE[self::JWT_COOKIE_KEY])) {
            return false;
        }

        $jwt = $_COOKIE[self::JWT_COOKIE_KEY];

        try{
            $decoded = JWT::decode($jwt, new Key($_ENV[self::JWT_SECRET_KEY], self::ALGO));
            return true;
        } catch(\Throwable $e) {
            return false;
        }
    }

    public function login(string $username): void
    {
        $payload = [
            'username' => $username
        ];

        $key = $_ENV[self::JWT_SECRET_KEY];

        $jwt = JWT::encode($payload, $key, self::ALGO);
        setcookie(self::JWT_COOKIE_KEY, $jwt, time() + (86400), "/");
    }

    public function logout(): void
    {
        setcookie(self::JWT_COOKIE_KEY, "", time() - 3600);
    }
}