<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\Get;
use App\Attributes\Post;
use App\Repositories\UserRepository;
use App\Services\AuthenticationServiceInterface;
use App\Services\Implementations\TOTPService;
use App\View;

class LoginController
{
    public function __construct(
        private AuthenticationServiceInterface $auth,
        private UserRepository $users,
        private View $view,
        private TOTPService $totp,
    ) {
    }

    #[Get('/login')]
    public function index()
    {
        if ($this->auth->isLoggedIn()) {
            $this->auth->logout();
        }
        return $this->view->make('login/index');
    }

    #[Post('/login')]
    public function login()
    {
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;

        $data = ['username' => $username, 'password' => $password];
        if (!$username) {
            $data['errors']['username'] = 'Username is required';
        }

        if (!$password) {
            $data['errors']['password'] = 'Password is required';
        }

        if (isset($data['errors'])) {
            return $this->view->make('login/index', $data);
        }

        $user = $this->users->findByUsername($username);
        if (!$user || !password_verify($password, $user['password'])) {
            return $this->view->make('login/index', ['errors' => ['password' => 'Invalid credentials']]);
        }

        return $this->view->make('login/two-factor', ['username' => $username]);
    }

    #[Post('/login/two-factor')]
    public function twoFactorConfirm()
    {
        $token =    $_POST['token'] ?? null;
        $username = $_POST['username'];

        $secret = $this->users->findByUsername($username)['totp_secret'];

        if (!$token || !$this->totp->validate($secret, $token)) {
            return $this->view->make('login/two-factor', ['username' => $username, 'errors' => ['token' => 'Invalid token.']]);
        }

        $this->auth->login($username);
        header("Location: " . "/profile");
    }

    #[Get('/logout')]
    public function logout()
    {
        $this->auth->logout();
        header("Location: " . "/login");
    }
}
