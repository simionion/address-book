<?php
declare(strict_types=1);

namespace Models;

use PDO;

class Contact
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllContacts(): array
    {
        return $this->pdo->query('SELECT * FROM contacts')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getContactById($id): array|null
    {
        $stmt = $this->pdo->prepare('SELECT * FROM contacts WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addContact($data): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO contacts (name, first_name, email, street, zip_code, city_id) VALUES (:name, :first_name, :email, :street, :zip_code, :city_id)');
        return $stmt->execute($data);
    }

    public function updateContact($id, $data): bool
    {
        $data['id'] = $id;
        $stmt = $this->pdo->prepare('UPDATE contacts SET name = :name, first_name = :first_name, email = :email, street = :street, zip_code = :zip_code, city_id = :city_id WHERE id = :id');
        return $stmt->execute($data);
    }

    public function deleteContact($id): bool
    {
        return $this->pdo->prepare('DELETE FROM contacts WHERE id = :id')->execute(['id' => $id]);
    }

}
