<?php
declare(strict_types=1);

namespace Models;

use Exception;
use PDO;

class Contact extends Model
{
    protected string $table = 'contacts';
    protected array $fillable = ['name', 'first_name', 'email', 'street', 'zip_code', 'city_id'];

    public function city(int $contactId): array|null
    {
        return $this->belongsTo(City::class, 'city_id', $contactId);
    }

    public function groups(int $contactId): array
    {
        return $this->belongsToMany(Group::class, 'group_contacts', 'contact_id', 'group_id', $contactId);
    }

    public function tags(int $contactId): array
    {
        return $this->belongsToMany(Tag::class, 'contact_tags', 'contact_id', 'tag_id', $contactId);
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

    public function detachGroups(int $contactId): void
    {
        $sql = "DELETE FROM group_contacts WHERE contact_id = :contact_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['contact_id' => $contactId]);
    }

    public function attachGroups(int $contactId, array $groupIds): void
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

    public function detachTags(int $contactId): void
    {
        $sql = "DELETE FROM contact_tags WHERE contact_id = :contact_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['contact_id' => $contactId]);
    }

    public function attachTags(int $contactId, array $tagIds): void
    {
        $sql = "INSERT INTO contact_tags (contact_id, tag_id) VALUES (:contact_id, :tag_id)";
        $stmt = $this->pdo->prepare($sql);

        foreach ($tagIds as $tagId) {
            $stmt->execute(['contact_id' => $contactId, 'tag_id' => $tagId]);
        }
    }

    public function getAllWithRelations(): array
    {

        $groups = '`groups`'; // `groups` is a reserved keyword in MySQL, IDE patching
        $sql = "
            SELECT contacts.*, cities.name AS city_name, groups.name AS group_name, tags.name AS tag_name
            FROM contacts
            LEFT JOIN cities ON contacts.city_id = cities.id
            LEFT JOIN group_contacts ON contacts.id = group_contacts.contact_id
            LEFT JOIN {$groups} ON group_contacts.group_id = groups.id
            LEFT JOIN contact_tags ON contacts.id = contact_tags.contact_id
            LEFT JOIN tags ON contact_tags.tag_id = tags.id
        ";

        $stmt = $this->pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->groupByContacts($results);
    }

    private function groupByContacts(array $results): array
    {
        $contacts = [];
        foreach ($results as $row) {
            $contactId = $row['id'];

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

            if (!empty($row['group_name']) && !in_array($row['group_name'], array_column($contacts[$contactId]['groups'], 'name'), true)) {
                $contacts[$contactId]['groups'][] = ['name' => $row['group_name']];
            }

            if (!empty($row['tag_name']) && !in_array($row['tag_name'], array_column($contacts[$contactId]['tags'], 'name'), true)) {
                $contacts[$contactId]['tags'][] = ['name' => $row['tag_name']];
            }
        }

        return array_values($contacts);
    }

    public function getByGroupIdsWithRelations(array $groupIds): array
    {
        $placeholders = $this->generateQuestionMarks(count($groupIds));
        $sql = "
            SELECT contacts.*, cities.name AS city_name, groups.name AS group_name, tags.name AS tag_name
            FROM contacts
            LEFT JOIN cities ON contacts.city_id = cities.id
            LEFT JOIN group_contacts ON contacts.id = group_contacts.contact_id
            LEFT JOIN `groups` ON group_contacts.group_id = groups.id
            LEFT JOIN contact_tags ON contacts.id = contact_tags.contact_id
            LEFT JOIN tags ON contact_tags.tag_id = tags.id
            WHERE group_contacts.group_id IN ({$placeholders})
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($groupIds);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->groupByContacts($results);
    }

    public function getByTagIdsWithRelations(array $tagIds): array
    {
        $placeholders = $this->generateQuestionMarks(count($tagIds));
        $sql = "
        SELECT contacts.*, cities.name AS city_name, groups.name AS group_name, tags.name AS tag_name
        FROM contacts
        LEFT JOIN cities ON contacts.city_id = cities.id
        LEFT JOIN group_contacts ON contacts.id = group_contacts.contact_id
        LEFT JOIN `groups` ON group_contacts.group_id = groups.id
        LEFT JOIN contact_tags ON contacts.id = contact_tags.contact_id
        LEFT JOIN tags ON contact_tags.tag_id = tags.id
        WHERE contact_tags.tag_id IN ({$placeholders})
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($tagIds);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->groupByContacts($results);
    }

}
