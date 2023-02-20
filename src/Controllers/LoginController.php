<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\Get;
use App\Attributes\Post;
use App\Repositories\UserRepository;
use App\Services\AuthenticationServiceInterface;
use App\View;

class LoginController
{
    public function __construct(
        private AuthenticationServiceInterface $auth,
        private UserRepository $users,
        private View $view
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
        if (!$user || $user['password'] !== $password) {
            return $this->view->make('login/index', ['errors' => ['password' => 'Invalid credentials']]);
        }

        $this->auth->authenticate($username);
        header("Location: " . "/two-factor");
    }

    #[Get('/two-factor')]
    public function twoFactor()
    {
        if (!$this->auth->isAuthenticated()) {
            header("Location: " . "/login");
        }

        return $this->view->make('login/two-factor');
    }

    #[Post('/two-factor')]
    public function twoFactorConfirm()
    {
        $token = $_POST['token'] ?? null;

        if ($token || false) {
            return $this->view->make('login/two-factor', ['errors' => 'Invalid token']);
        }

        $this->auth->login();
        header("Location: " . "/");
    }
}
