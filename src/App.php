<?php

declare(strict_types=1);

namespace App;

use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use App\Exceptions\RouteNotFoundException;
use App\Services\AuthenticationServiceInterface;
use App\Services\EmailServiceInterface;
use App\Services\Implementations\SendgridEmailService;
use App\Services\Implementations\SessionAuthenticationService;
use PDOException;

class App
{
    private readonly DB $db;
    private readonly View $view;

    private function initDB()
    {
        try {
            $this->db->exec("CREATE TABLE Users(username VARCHAR(255) PRIMARY KEY, password VARCHAR(255), totp_secret VARCHAR(255));");
        } catch (PDOException $e) {
        }
    }

    private function setupTwig()
    {
        $loader = new  \Twig\Loader\FilesystemLoader(__DIR__ . '/../public/templates/');
        $twig = new \Twig\Environment($loader);

        return new View($twig);
    }

    private function registerControllers()
    {
        $this->router
            ->registerControllers([
                HomeController::class,
                LoginController::class,
                RegisterController::class
            ]);
    }

    public function __construct(
        private Container $container,
        private Router $router,
        private array $config,
        private array $request
    ) {
        $this->db = new DB($config);
        $this->view = $this->setupTwig();

        $this->container->set(DB::class, fn () => $this->db);
        $this->container->set(View::class, fn () => $this->view);

        $this->container->set(EmailServiceInterface::class, SendgridEmailService::class);
        $this->container->set(AuthenticationServiceInterface::class, SessionAuthenticationService::class);

        $this->registerControllers();
        $this->initDB();
    }

    public function run()
    {
        try {
            echo $this->router->resolve(strtoupper($this->request['method']), $this->request['uri']);
        } catch (RouteNotFoundException $e) {

            http_response_code(404);
            echo "<h1>Not found</h1>";
        }
    }
}
