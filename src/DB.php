<?php

declare(strict_types=1);

namespace App;

use PDO;

class DB extends PDO
{
    // public readonly PDO $pdo;

    public function __construct(array $config)
    {
        $host     = $config['host'];
        $database = $config['database'];
        $user     = $config['username'];
        $password = $config['password'];

        parent::__construct("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password);
        // $this->pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password);
    }
}
