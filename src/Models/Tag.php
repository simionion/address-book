<?php
declare(strict_types=1);

namespace Models;

use PDO;

class Tag extends Model
{
    protected string $table = 'tags';
    protected array $fillable = ['name'];

    public function contacts(int $tagId): array
    {
        return $this->belongsToMany(Contact::class, 'contact_tags', 'tag_id', 'contact_id', $tagId);
    }

    public function whereHasContacts(): array
    {
        $sql = "
            SELECT {$this->table}.* FROM {$this->table}
            WHERE EXISTS (
                SELECT 1 FROM contact_tags
                WHERE contact_tags.tag_id = {$this->table}.id
            )
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
