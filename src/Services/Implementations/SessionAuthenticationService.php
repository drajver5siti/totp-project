<?php

declare(strict_types=1);

namespace App\Services\Implementations;

use App\Services\AuthenticationServiceInterface;

class SessionAuthenticationService implements AuthenticationServiceInterface
{
    public function getUsername(): string
    {
        return $_SESSION['username'];
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['username']);
    }

    public function login(string $username): void
    {
        $_SESSION['username'] = $username;
    }

    public function logout(): void
    {
        session_destroy();
    }
}
