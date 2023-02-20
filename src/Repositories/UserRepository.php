<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DB;
use PDO;

class UserRepository
{

    public function __construct(private DB $db)
    {
    }

    public function findAll()
    {
        return $this->db->query("SELECT * FROM Users")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUsername(string $username)
    {
        $statement = $this->db->prepare("SELECT * FROM Users WHERE username=:username");
        $statement->execute(['username' => $username]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function save(array $user)
    {
    }
}
