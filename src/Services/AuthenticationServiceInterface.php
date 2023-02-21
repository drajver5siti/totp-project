<?php

declare(strict_types=1);

namespace App\Services;

interface AuthenticationServiceInterface
{
    public function getUsername(): string;
    public function isLoggedIn(): bool;

    public function login(string $username): void;
    public function logout(): void;
}
