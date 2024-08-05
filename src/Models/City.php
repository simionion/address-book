<?php
declare(strict_types=1);

namespace Models;

use PDO;

class City
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllCities(): array
    {
        return $this->pdo->query('SELECT * FROM cities')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCityById($id): array|null
    {
        $stmt = $this->pdo->prepare('SELECT * FROM cities WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
