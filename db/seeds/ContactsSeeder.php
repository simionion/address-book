<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class ContactsSeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Doe',
                'first_name' => 'John',
                'email' => 'john.doe@example.com',
                'street' => '123 Main St',
                'zip_code' => '12345',
                'city_id' => 1
            ],
            [
                'name' => 'Smith',
                'first_name' => 'Jane',
                'email' => 'jane.smith@example.com',
                'street' => '456 Elm St',
                'zip_code' => '67890',
                'city_id' => 2
            ],
            [
                'name' => 'Brown',
                'first_name' => 'Charlie',
                'email' => 'charlie.brown@example.com',
                'street' => '789 Oak St',
                'zip_code' => '11223',
                'city_id' => 3
            ],
            [
                'name' => 'Johnson',
                'first_name' => 'Emily',
                'email' => 'emily.johnson@example.com',
                'street' => '101 Maple St',
                'zip_code' => '33445',
                'city_id' => 4
            ],
            [
                'name' => 'Williams',
                'first_name' => 'Michael',
                'email' => 'michael.williams@example.com',
                'street' => '202 Pine St',
                'zip_code' => '55667',
                'city_id' => 5
            ],
            [
                'name' => 'Jones',
                'first_name' => 'Sarah',
                'email' => 'sarah.jones@example.com',
                'street' => '303 Birch St',
                'zip_code' => '77889',
                'city_id' => 6
            ],
            [
                'name' => 'Taylor',
                'first_name' => 'David',
                'email' => 'david.taylor@example.com',
                'street' => '404 Cedar St',
                'zip_code' => '99001',
                'city_id' => 7
            ],
        ];

        $contacts = $this->table('contacts');
        $contacts->insert($data)
                 ->saveData();
    }
}
