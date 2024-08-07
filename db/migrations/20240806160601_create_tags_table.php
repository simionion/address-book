<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTagsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('tags');
        $table->addColumn('name', 'string', ['limit' => 255])
            ->create();
    }
}
