<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateContactsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('contacts');
        $table->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('first_name', 'string', ['limit' => 255])
            ->addColumn('email', 'string', ['limit' => 255])
            ->addColumn('street', 'string', ['limit' => 255])
            ->addColumn('zip_code', 'string', ['limit' => 10])
            ->addColumn('city_id', 'integer', ['signed' => false])
            ->addForeignKey('city_id', 'cities', 'id', ['delete' => 'RESTRICT', 'update' => 'NO_ACTION'])
            ->create();
    }
}
