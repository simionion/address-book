<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class ContactsSeeder extends AbstractSeed
{
    public function run(): void
    {
        // Insert initial contacts
        $contacts = [
            ['id' => 1, 'name' => 'CA1', 'first_name' => 'John', 'email' => 'john@example.com', 'street' => '123 Main St', 'zip_code' => '12345', 'city_id' => 1],
            ['id' => 2, 'name' => 'CA2', 'first_name' => 'Jane', 'email' => 'jane@example.com', 'street' => '456 Elm St', 'zip_code' => '67890', 'city_id' => 2],
            ['id' => 3, 'name' => 'CB1', 'first_name' => 'Jim', 'email' => 'jim@example.com', 'street' => '789 Oak St', 'zip_code' => '11223', 'city_id' => 1],
            ['id' => 4, 'name' => 'CB2', 'first_name' => 'Jill', 'email' => 'jill@example.com', 'street' => '101 Pine St', 'zip_code' => '44556', 'city_id' => 2],
            ['id' => 5, 'name' => 'CC1', 'first_name' => 'Jack', 'email' => 'jack@example.com', 'street' => '202 Maple St', 'zip_code' => '77889', 'city_id' => 1],
            ['id' => 6, 'name' => 'CC2', 'first_name' => 'Jenny', 'email' => 'jenny@example.com', 'street' => '303 Birch St', 'zip_code' => '99100', 'city_id' => 2],
            ['id' => 7, 'name' => 'CD1', 'first_name' => 'Joe', 'email' => 'joe@example.com', 'street' => '404 Cedar St', 'zip_code' => '22334', 'city_id' => 1],
            ['id' => 8, 'name' => 'CD2', 'first_name' => 'Julia', 'email' => 'julia@example.com', 'street' => '505 Redwood St', 'zip_code' => '55667', 'city_id' => 2],
        ];
        $this->table('contacts')->insert($contacts)->saveData();

        // Insert initial groups
        $groups = [
            ['id' => 1, 'name' => 'Group A'],
            ['id' => 2, 'name' => 'Group AA'],
            ['id' => 3, 'name' => 'Group B'],
            ['id' => 4, 'name' => 'Group C'],
            ['id' => 5, 'name' => 'Group D'],
        ];
        $this->table('groups')->insert($groups)->saveData();

        // Insert initial group contacts
        $groupContacts = [
            ['group_id' => 1, 'contact_id' => 1],
            ['group_id' => 1, 'contact_id' => 2],
            ['group_id' => 2, 'contact_id' => 1],
            ['group_id' => 2, 'contact_id' => 2],
            ['group_id' => 3, 'contact_id' => 3],
            ['group_id' => 3, 'contact_id' => 4],
            ['group_id' => 4, 'contact_id' => 5],
            ['group_id' => 4, 'contact_id' => 6],
            ['group_id' => 5, 'contact_id' => 7],
            ['group_id' => 5, 'contact_id' => 8],
        ];
        $this->table('group_contacts')->insert($groupContacts)->saveData();

        // Insert initial group inheritance
        $groupInheritance = [
            ['parent_group_id' => 1, 'child_group_id' => 2],
            ['parent_group_id' => 1, 'child_group_id' => 4],
            ['parent_group_id' => 2, 'child_group_id' => 5],
            ['parent_group_id' => 3, 'child_group_id' => 5],
            ['parent_group_id' => 4, 'child_group_id' => 5],
        ];
        $this->table('group_inheritance')->insert($groupInheritance)->saveData();
    }

}
