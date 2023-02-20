<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\Get;
use App\DB;
use App\Services\AuthenticationServiceInterface;
use App\Services\EmailServiceInterface;
use App\Services\Implementations\TOTPService;
use App\View;
use chillerlan\QRCode\QRCode;
use OTPHP\TOTP;
use PDO;

class HomeController
{
    public function __construct(
        private EmailServiceInterface $emailService,
        private AuthenticationServiceInterface $auth,
        private DB $db,
        private View $view,
        private TOTPService $totp
    ) {
    }

    #[Get('/')]
    public function index()
    {
        if (!$this->auth->isLoggedIn()) {
            header("Location: /login");
        }

        // $sql = "INSERT INTO Users VALUES(:username, :password, :totp_token)";
        // $data = [
        //     'username' => 'ivan.pavlovski',
        //     'password' => 'test',
        //     'totp_token' => 'yejajsldjwjlda'
        // ];

        // $stmnt = $this->db->prepare($sql);
        // $stmnt->execute($data);

        $stmnt = $this->db->prepare("SELECT * FROM Users WHERE username=:username");
        $stmnt->execute(['username' => 'ivan.pavlovski']);
        $user = $stmnt->fetch(PDO::FETCH_ASSOC);

        echo "<pre>";
        print_r($user);
        echo "</pre>";
        return "";

        // return $this->view->make('home/index', ['user' => 'ivan']);
    }

    #[Get('/test')]
    public function test()
    {
        $secret = "UJEG3OB36O7ZSSF4EZTIEJG3QEW2PFB6";
        $token = $this->totp->generateOTPToken("UJEG3OB36O7ZSSF4EZTIEJG3QEW2PFB6");

        echo $token . "<br/>";

        $render = (new QRCode)->render($secret);
        echo '<img src="' . $render . '"/>';

        // echo $secret;
    }
}
