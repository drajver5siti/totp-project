<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\Get;
use App\Attributes\Post;
use App\Repositories\UserRepository;
use App\Services\AuthenticationServiceInterface;
use App\View;

class RegisterController
{
    public function __construct(
        private AuthenticationServiceInterface $auth,
        private UserRepository $users,
        private View $view,
    ) {
    }

    #[Get("/register")]
    public function index()
    {
        return $this->view->make("register/index");
    }

    #[Post("/register")]
    public function register()
    {
        if ($this->auth->isLoggedIn()) {
            $this->auth->logout();
        }

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
            return $this->view->make('register/index', $data);
        }

        if (!!$this->users->findByUsername($username)) {
            return $this->view->make('register/index', ['errors' => ['password' => 'User already exists']]);
        }


        return $this->view->make('register/index', ['username' => $username, 'password' => $password, '2fa' => null]);
    }
}
