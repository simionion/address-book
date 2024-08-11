<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateGroupContactsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('group_contacts');
        $table->addColumn('group_id', 'integer', ['signed' => false])
            ->addColumn('contact_id', 'integer', ['signed' => false])
            ->addForeignKey('group_id', 'groups_table', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('contact_id', 'contacts', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}

