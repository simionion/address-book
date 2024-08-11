<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateGroupInheritanceTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('group_inheritance');
        $table->addColumn('parent_group_id', 'integer', ['signed' => false])
            ->addColumn('child_group_id', 'integer', ['signed' => false])
            ->addForeignKey('parent_group_id', 'groups_table', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('child_group_id', 'groups_table', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
