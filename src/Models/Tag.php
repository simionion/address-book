<?php
declare(strict_types=1);

namespace Models;

use PDO;

class Tag extends Model
{
    protected string $table = 'tags';
    protected array $fillable = ['name'];


    // Retrieve all tags that have at least one contact associated
    public function whereHasContacts(): array
    {
        $sql = "
            SELECT tags.* FROM {$this->table}
            WHERE EXISTS (
                SELECT 1 FROM contact_tags
                WHERE contact_tags.tag_id = tags.id
            )
        ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
