<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateGroupsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('groups_table');
        $table->addColumn('name', 'string', ['limit' => 255])
            ->create();
    }
}
