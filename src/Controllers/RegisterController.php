<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\Get;
use App\Attributes\Post;
use App\Repositories\UserRepository;
use App\Services\AuthenticationServiceInterface;
use App\Services\Implementations\TOTPService;
use App\View;
use chillerlan\QRCode\QRCode;

class RegisterController
{
    public function __construct(
        private AuthenticationServiceInterface $auth,
        private UserRepository $users,
        private View $view,
        private TOTPService $totp
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

        $password = password_hash($password, PASSWORD_BCRYPT);

        $secret = $this->totp->generateSharedSecret();
        $qrpath = $this->totp->generateQRPath($secret, $username);
        $qr = (new QRCode)->render($qrpath);

        return $this->view->make('register/two-factor', ['username' => $username, 'password' => $password, 'secret' => $secret, 'qr' => $qr]);
    }

    #[Post('/register/two-factor')]
    public function twoFactorConfirm()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $secret   = $_POST['secret'];
        $token    = $_POST['token'];
        $qr       = $_POST['qr'];


        if ($this->totp->generateOTPToken($secret) !== $token) {
            return $this->view->make(
                'register/two-factor',
                [
                    'username' => $username,
                    'password' => $password,
                    'secret' => $secret,
                    'qr' => $qr,
                    'errors' => ['token' => 'Invalid token.']
                ]
            );
        }

        $this->users->save(['username' => $username, 'password' =>  $password, 'totp_secret' => $secret]);
        $this->auth->login($username);
        header("Location: " . "/");
    }
}
