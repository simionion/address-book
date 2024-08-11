<?php
declare(strict_types=1);

namespace Models;

use PDO;

abstract class Model
{
    protected PDO $pdo;
    protected string $table;
    protected array $fillable = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Basic CRUD Operations

    public function all(): array
    {
        return $this->pdo->query("SELECT * FROM {$this->table}")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): array|null
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function save(array $data): array|null
    {
        $fields = $this->getFieldsByFillable($data);
        $columns = $this->getColumnsAsString($fields);
        $placeholders = $this->getPlaceholdersAsString($fields);

        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        if ($stmt->execute($fields)) {
            return $this->find((int)$this->pdo->lastInsertId());
        }
        return null;
    }

    public function update(int $id, array $data): bool
    {
        $fields = $this->getFieldsByFillable($data);
        $columns = $this->getColumnsWithPlaceholdersAsString($fields);

        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET {$columns} WHERE id = :id");
        $fields['id'] = $id;

        return $stmt->execute($fields);
    }

    public function delete(int $id): bool
    {
        return $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id")->execute(['id' => $id]);
    }

    // Utility Methods

    public function getFieldsByFillable(array $data): array
    {
        return array_intersect_key($data, array_flip($this->fillable));
    }

    public function getColumnsAsString(array $fields): string
    {
        return implode(', ', array_keys($fields));
    }

    public function getPlaceholdersAsString(array $fields): string
    {
        return implode(', ', array_map(static fn($col) => ":{$col}", array_keys($fields)));
    }

    public function getColumnsWithPlaceholdersAsString(array $fields): string
    {
        return implode(', ', array_map(static fn($col) => "{$col} = :{$col}", array_keys($fields)));
    }

    public function generateQuestionMarks(int $count): string
    {
        return implode(', ', array_fill(0, $count, '?'));
    }
}
