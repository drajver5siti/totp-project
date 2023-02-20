<?php

declare(strict_types=1);

namespace App\Services;

interface AuthenticationServiceInterface
{
    public function isAuthenticated(): bool;
    public function isLoggedIn(): bool;

    public function authenticate(string $username): void;
    public function login(): void;
    public function logout(): void;
}
