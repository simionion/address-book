<?php
declare(strict_types=1);

namespace Models;

use PDO;

abstract class Model
{
    protected PDO $pdo;
    protected string $table;
    protected array $fillable = [];
    protected array $relations = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->loadRelations($results);
    }

    private function loadRelations(array $results): array
    {
        if (empty($this->relations)) {
            return $results;
        }

        foreach ($results as &$result) {
            foreach ($this->relations as $relation) {
                if (method_exists($this, $relation)) {
                    $result[$relation] = $this->{$relation}($result['id']);
                }
            }
        }

        return $results;
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

    /**
     * @param array $data
     * @return array $fields - Filters $data for fields that are in $fillable
     */

    public function getFieldsByFillable(array $data): array
    {
        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * @param array $fields
     * @return string "name, email" - Returns a string of columns separated by a comma
     */

    public function getColumnsAsString(array $fields): string
    {
        return implode(', ', array_keys($fields));
    }

    /**
     * @param array $fields
     * @return string ":name, :email" - Returns a string of placeholders separated by a comma
     */
    public function getPlaceholdersAsString(array $fields): string
    {
        return implode(', ', array_map(static fn($col) => ":{$col}", array_keys($fields)));
    }

    public function find(int $id): array|null
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return $this->loadRelations([$result])[0];
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

    /**
     * @param array $fields
     * @return string "name = :name, email = :email" - Returns a string of columns and placeholders separated by a comma
     */
    public function getColumnsWithPlaceholdersAsString(array $fields): string
    {
        return implode(', ', array_map(static fn($col) => "{$col} = :{$col}", array_keys($fields)));
    }

    public function delete(int $id): bool
    {
        return $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id")->execute(['id' => $id]);
    }

    public function with(array $relations): self
    {
        $this->relations = $relations;
        return $this;
    }

    /**
     * @param int $count
     * @return string "?, ?, ?, ?" - Returns a string of question marks separated by a comma
     */
    public function generateQuestionMarks(int $count): string
    {
        return implode(', ', array_fill(0, $count, '?'));
    }

    protected function hasMany(string $relatedClass, string $foreignKey, int $id): array
    {
        $related = new $relatedClass($this->pdo);
        $stmt = $this->pdo->prepare("SELECT * FROM {$related->table} WHERE {$foreignKey} = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function belongsTo(string $relatedClass, string $foreignKey, int $id): array|null
    {
        $related = new $relatedClass($this->pdo);
        $stmt = $this->pdo->prepare("SELECT * FROM {$related->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    protected function belongsToMany(string $relatedClass, string $pivotTable, string $foreignPivotKey, string $relatedPivotKey, int $id): array
    {
        $related = new $relatedClass($this->pdo);
        $stmt = $this->pdo->prepare("
            SELECT {$related->table}.* FROM {$related->table}
            JOIN {$pivotTable} ON {$related->table}.id = {$pivotTable}.{$relatedPivotKey}
            WHERE {$pivotTable}.{$foreignPivotKey} = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}
