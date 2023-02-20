<?php

declare(strict_types=1);

namespace App\Services\Implementations;

use App\Services\AuthenticationServiceInterface;

class SessionAuthenticationService implements AuthenticationServiceInterface
{
    public function isAuthenticated(): bool
    {
        return isset($_SESSION['username']);
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['username']) && isset($_SESSION['2fa']);
    }

    public function authenticate(string $username): void
    {
        $_SESSION['username'] = $username;
    }

    public function login(): void
    {
        $_SESSION['2fa'] = true;
    }

    public function logout(): void
    {
        session_destroy();
    }
}
