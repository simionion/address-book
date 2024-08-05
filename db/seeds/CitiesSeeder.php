<?php
declare(strict_types=1);


use Phinx\Seed\AbstractSeed;

class CitiesSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Zurich'],
            ['name' => 'Geneva'],
            ['name' => 'Basel'],
            ['name' => 'Lausanne'],
            ['name' => 'Bern'],
            ['name' => 'Winterthur'],
            ['name' => 'Lucerne'],
            ['name' => 'St. Gallen'],
            ['name' => 'Lugano'],
            ['name' => 'Biel/Bienne'],
            ['name' => 'Thun'],
            ['name' => 'Koniz'],
            ['name' => 'La Chaux-de-Fonds'],
            ['name' => 'Schaffhausen'],
            ['name' => 'Fribourg'],
            ['name' => 'Chur'],
            ['name' => 'Neuchatel'],
            ['name' => 'Vernier'],
            ['name' => 'Uster'],
            ['name' => 'Sion'],
        ];

        $cities = $this->table('cities');
        $cities->insert($data)
            ->saveData();
    }
}
