<?php
declare(strict_types=1);

namespace Models;

use Exception;
use PDO;

class Group extends Model
{
    protected string $table = '`groups`'; // `groups` is a reserved keyword in MySQL
    protected array $fillable = ['name'];

    public function contacts(int $groupId): array
    {
        return $this->belongsToMany(Contact::class, 'group_contacts', 'group_id', 'contact_id', $groupId);
    }

    public function parentGroups(int $groupId): array
    {
        return $this->belongsToMany(__CLASS__, 'group_inheritance', 'child_group_id', 'parent_group_id', $groupId);
    }

    public function childGroups(int $groupId): array
    {
        return $this->belongsToMany(__CLASS__, 'group_inheritance', 'parent_group_id', 'child_group_id', $groupId);
    }

    public function getAllWithChildGroups(): array
    {
        $sql = "
            SELECT g.*, cg.child_group_id, child.name AS child_group_name
            FROM {$this->table} g
            LEFT JOIN group_inheritance cg ON g.id = cg.parent_group_id
            LEFT JOIN {$this->table} child ON cg.child_group_id = child.id
        ";

        $stmt = $this->pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->groupByGroups($results);
    }

    private function groupByGroups(array $results): array
    {
        $groups = [];
        foreach ($results as $row) {
            $groupId = $row['id'];

            if (!isset($groups[$groupId])) {
                $groups[$groupId] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'child_groups' => [],
                ];
            }

            if (!empty($row['child_group_id']) && !in_array($row['child_group_id'], array_column($groups[$groupId]['child_groups'], 'id'), true)) {
                $groups[$groupId]['child_groups'][] = [
                    'id' => $row['child_group_id'],
                    'name' => $row['child_group_name'],
                ];
            }
        }

        return array_values($groups);
    }

    /**
     * @throws Exception
     */
    public function syncParentGroups(int $groupId, array $parentGroupIds): void
    {
        $this->pdo->beginTransaction();
        try {
            $this->detachParentGroups($groupId);
            $this->attachParentGroups($groupId, $parentGroupIds);
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function detachParentGroups(int $groupId): void
    {
        $sql = "DELETE FROM group_inheritance WHERE child_group_id = :child_group_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['child_group_id' => $groupId]);
    }

    public function attachParentGroups(int $groupId, array $parentGroupIds): void
    {
        $sql = "INSERT INTO group_inheritance (parent_group_id, child_group_id) VALUES (:parent_group_id, :child_group_id)";
        $stmt = $this->pdo->prepare($sql);

        foreach ($parentGroupIds as $parentGroupId) {
            $stmt->execute(['parent_group_id' => $parentGroupId, 'child_group_id' => $groupId]);
        }
    }

    /**
     * @throws Exception
     */
    public function syncChildGroups(int $groupId, array $childGroupIds): void
    {
        $this->pdo->beginTransaction();
        try {
            $this->detachChildGroups($groupId);
            $this->attachChildGroups($groupId, $childGroupIds);
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function detachChildGroups(int $groupId): void
    {
        $sql = "DELETE FROM group_inheritance WHERE parent_group_id = :parent_group_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['parent_group_id' => $groupId]);
    }

    public function attachChildGroups(int $groupId, array $childGroupIds): void
    {
        $sql = "INSERT INTO group_inheritance (parent_group_id, child_group_id) VALUES (:parent_group_id, :child_group_id)";
        $stmt = $this->pdo->prepare($sql);

        foreach ($childGroupIds as $childGroupId) {
            $stmt->execute(['parent_group_id' => $groupId, 'child_group_id' => $childGroupId]);
        }
    }

    public function whereHasContacts(): array
    {
        $sql = "
            SELECT {$this->table}.* FROM {$this->table}
            WHERE EXISTS (
                SELECT 1 FROM group_contacts
                WHERE group_contacts.group_id = {$this->table}.id
            )
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
