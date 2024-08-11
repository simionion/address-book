<?php
declare(strict_types=1);

namespace Models;

use Exception;
use PDO;

class Contact extends Model
{
    protected string $table = 'contacts';
    protected array $fillable = ['name', 'first_name', 'email', 'street', 'zip_code', 'city_id'];

    public function getContactWithRelations(int $contactId): array
    {
        $sql = "
            SELECT contacts.*, cities.name AS city_name,
                   groups_table.id AS group_id, groups_table.name AS group_name,
                   tags.id AS tag_id, tags.name AS tag_name
            FROM contacts
            LEFT JOIN cities ON contacts.city_id = cities.id
            LEFT JOIN group_contacts ON contacts.id = group_contacts.contact_id
            LEFT JOIN groups_table ON group_contacts.group_id = groups_table.id
            LEFT JOIN contact_tags ON contacts.id = contact_tags.contact_id
            LEFT JOIN tags ON contact_tags.tag_id = tags.id
            WHERE contacts.id = :contact_id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['contact_id' => $contactId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->groupByContacts($results)[0] ?? [];
    }

    private function groupByContacts(array $results): array
    {
        $contacts = [];

        foreach ($results as $row) {
            $contactId = $row['id'];

            // Initialize the contact if it's not already in the array
            if (!isset($contacts[$contactId])) {
                $contacts[$contactId] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'first_name' => $row['first_name'],
                    'email' => $row['email'],
                    'street' => $row['street'],
                    'zip_code' => $row['zip_code'],
                    'city_id' => $row['city_id'],
                    'city_name' => $row['city_name'],
                    'groups' => [],
                    'tags' => [],
                ];
            }

            // Add the group to the contact's groups array
            if ($row['group_id'] !== null && !in_array($row['group_name'], array_column($contacts[$contactId]['groups'], 'name'), true)) {
                $contacts[$contactId]['groups'][] = [
                    'id' => $row['group_id'],
                    'name' => $row['group_name'],
                ];
            }

            // Add the tag to the contact's tags array
            if ($row['tag_id'] !== null && !in_array($row['tag_name'], array_column($contacts[$contactId]['tags'], 'name'), true)) {
                $contacts[$contactId]['tags'][] = [
                    'id' => $row['tag_id'],
                    'name' => $row['tag_name'],
                ];
            }
        }

        return array_values($contacts);
    }

    /**
     * @throws Exception
     */
    public function syncGroups(int $contactId, array $groupIds): void
    {
        $this->pdo->beginTransaction();
        try {
            $this->detachGroups($contactId);
            $this->attachGroups($contactId, $groupIds);
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function detachGroups(int $contactId): void
    {
        $sql = "DELETE FROM group_contacts WHERE contact_id = :contact_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['contact_id' => $contactId]);
    }

    private function attachGroups(int $contactId, array $groupIds): void
    {
        $sql = "INSERT INTO group_contacts (contact_id, group_id) VALUES (:contact_id, :group_id)";
        $stmt = $this->pdo->prepare($sql);

        foreach ($groupIds as $groupId) {
            $stmt->execute(['contact_id' => $contactId, 'group_id' => $groupId]);
        }
    }

    /**
     * @throws Exception
     */
    public function syncTags(int $contactId, array $tagIds): void
    {
        $this->pdo->beginTransaction();
        try {
            $this->detachTags($contactId);
            $this->attachTags($contactId, $tagIds);
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function detachTags(int $contactId): void
    {
        $sql = "DELETE FROM contact_tags WHERE contact_id = :contact_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['contact_id' => $contactId]);
    }

    private function attachTags(int $contactId, array $tagIds): void
    {
        $sql = "INSERT INTO contact_tags (contact_id, tag_id) VALUES (:contact_id, :tag_id)";
        $stmt = $this->pdo->prepare($sql);

        foreach ($tagIds as $tagId) {
            $stmt->execute(['contact_id' => $contactId, 'tag_id' => $tagId]);
        }
    }

    public function getAllWithRelations(): array
    {
        $sql = "
            SELECT contacts.*, cities.name AS city_name,
                   groups_table.id AS group_id, groups_table.name AS group_name,
                   tags.id AS tag_id, tags.name AS tag_name
            FROM contacts
            LEFT JOIN cities ON contacts.city_id = cities.id
            LEFT JOIN group_contacts ON contacts.id = group_contacts.contact_id
            LEFT JOIN groups_table ON group_contacts.group_id = groups_table.id
            LEFT JOIN contact_tags ON contacts.id = contact_tags.contact_id
            LEFT JOIN tags ON contact_tags.tag_id = tags.id
        ";

        $stmt = $this->pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->groupByContacts($results);
    }


    public function getByTagIdsWithRelations(array $tagIds): array
    {
        $placeholders = $this->generateQuestionMarks(count($tagIds));
        $sql = "
            SELECT contacts.*, cities.name AS city_name,
                   groups_table.id AS group_id, groups_table.name AS group_name,
                   tags.id AS tag_id, tags.name AS tag_name
            FROM contacts
            LEFT JOIN cities ON contacts.city_id = cities.id
            LEFT JOIN group_contacts ON contacts.id = group_contacts.contact_id
            LEFT JOIN groups_table ON group_contacts.group_id = groups_table.id
            LEFT JOIN contact_tags ON contacts.id = contact_tags.contact_id
            LEFT JOIN tags ON contact_tags.tag_id = tags.id
            WHERE contact_tags.tag_id IN ($placeholders)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($tagIds);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->groupByContacts($results);
    }

    public function getContactsByGroupWithRelations(int $groupId): array
    {
        $sql = "
                WITH RECURSIVE group_hierarchy AS (
                    SELECT groups_table.id, groups_table.name
                    FROM groups_table
                    WHERE groups_table.id = :group_id
                    UNION ALL
                    SELECT parent_groups.id, parent_groups.name
                    FROM groups_table parent_groups
                    INNER JOIN group_inheritance ON parent_groups.id = group_inheritance.parent_group_id
                    JOIN group_hierarchy ON group_hierarchy.id = group_inheritance.child_group_id
                )
                SELECT contacts.*, cities.name AS city_name, 
                       groups_table.id AS group_id, groups_table.name AS group_name,
                       tags.id AS tag_id, tags.name AS tag_name
                FROM contacts
                LEFT JOIN cities ON contacts.city_id = cities.id
                LEFT JOIN group_contacts ON contacts.id = group_contacts.contact_id
                LEFT JOIN groups_table ON group_contacts.group_id = groups_table.id
                LEFT JOIN group_hierarchy ON group_contacts.group_id = group_hierarchy.id
                LEFT JOIN contact_tags ON contacts.id = contact_tags.contact_id
                LEFT JOIN tags ON contact_tags.tag_id = tags.id
                WHERE group_contacts.group_id IN (SELECT id FROM group_hierarchy)
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['group_id' => $groupId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->groupByContacts($results);
    }
}
