<?php
declare(strict_types=1);

namespace Models;

use Exception;
use PDO;

class Group extends Model
{
    protected string $table = 'groups_table'; // groups_table is a reserved keyword in MySQL
    protected array $fillable = ['name'];

    public function getAllWithChildGroups(): array
    {
        $sql = "
            SELECT groups_table.*, group_inheritance.child_group_id, groups_child.name AS child_group_name
            FROM groups_table
            LEFT JOIN group_inheritance ON groups_table.id = group_inheritance.parent_group_id
            LEFT JOIN groups_table AS groups_child ON group_inheritance.child_group_id = groups_child.id
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

    public function getGroupWithRelations(int $groupId): array
    {
        $sql = "
            SELECT groups_table.*, parent_groups_table.id AS parent_group_id, parent_groups_table.name AS parent_group_name,
                   child_groups_table.id AS child_group_id, child_groups_table.name AS child_group_name
            FROM groups_table
            LEFT JOIN group_inheritance AS parent_inheritance ON parent_inheritance.child_group_id = groups_table.id
            LEFT JOIN groups_table AS parent_groups_table ON parent_inheritance.parent_group_id = parent_groups_table.id
            LEFT JOIN group_inheritance AS child_inheritance ON child_inheritance.parent_group_id = groups_table.id
            LEFT JOIN groups_table AS child_groups_table ON child_inheritance.child_group_id = child_groups_table.id
            WHERE groups_table.id = :group_id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['group_id' => $groupId]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->groupRelations($results);
    }

    private function groupRelations(array $results): array
    {
        $group = [];
        foreach ($results as $row) {
            if (empty($group)) {
                $group = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'parent_groups' => [],
                    'child_groups' => [],
                ];
            }

            if (!empty($row['parent_group_id']) && !in_array($row['parent_group_id'], array_column($group['parent_groups'], 'id'), true)) {
                $group['parent_groups'][] = [
                    'id' => $row['parent_group_id'],
                    'name' => $row['parent_group_name'],
                ];
            }

            if (!empty($row['child_group_id']) && !in_array($row['child_group_id'], array_column($group['child_groups'], 'id'), true)) {
                $group['child_groups'][] = [
                    'id' => $row['child_group_id'],
                    'name' => $row['child_group_name'],
                ];
            }
        }

        return $group;
    }

    /**
     * @throws Exception
     */
    public function syncGroupRelations(int $groupId, array $parentGroupIds, array $childGroupIds): void
    {
        $this->pdo->beginTransaction();
        try {
            $this->detachParentGroups($groupId);
            $this->attachParentGroups($groupId, $parentGroupIds);
            $this->detachChildGroups($groupId);
            $this->attachChildGroups($groupId, $childGroupIds);
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function detachParentGroups(int $groupId): void
    {
        $sql = "DELETE FROM group_inheritance WHERE child_group_id = :child_group_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['child_group_id' => $groupId]);
    }

    private function attachParentGroups(int $groupId, array $parentGroupIds): void
    {
        $sql = "INSERT INTO group_inheritance (parent_group_id, child_group_id) VALUES (:parent_group_id, :child_group_id)";
        $stmt = $this->pdo->prepare($sql);

        foreach ($parentGroupIds as $parentGroupId) {
            $stmt->execute(['parent_group_id' => $parentGroupId, 'child_group_id' => $groupId]);
        }
    }

    private function detachChildGroups(int $groupId): void
    {
        $sql = "DELETE FROM group_inheritance WHERE parent_group_id = :parent_group_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['parent_group_id' => $groupId]);
    }

    private function attachChildGroups(int $groupId, array $childGroupIds): void
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
        SELECT DISTINCT groups_table.* 
        FROM groups_table
        LEFT JOIN group_contacts ON group_contacts.group_id = groups_table.id
        LEFT JOIN group_inheritance AS parent_inheritance ON parent_inheritance.parent_group_id = groups_table.id
        LEFT JOIN group_inheritance AS child_inheritance ON child_inheritance.child_group_id = groups_table.id
        LEFT JOIN group_contacts AS parent_contacts ON parent_contacts.group_id = child_inheritance.child_group_id
        LEFT JOIN group_contacts AS child_contacts ON child_contacts.group_id = parent_inheritance.parent_group_id
        WHERE group_contacts.contact_id IS NOT NULL
        OR parent_contacts.contact_id IS NOT NULL
        OR child_contacts.contact_id IS NOT NULL
    ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
