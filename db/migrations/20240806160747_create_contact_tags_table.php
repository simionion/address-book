<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateContactTagsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('contact_tags');
        $table->addColumn('contact_id', 'integer', ['signed' => false])
            ->addColumn('tag_id', 'integer', ['signed' => false])
            ->addForeignKey('contact_id', 'contacts', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('tag_id', 'tags', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
