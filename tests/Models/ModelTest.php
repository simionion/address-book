<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Models\City;
use Models\Contact;
use Models\Group;
use Models\Tag;

class ModelTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->createTables();
    }

    private function createTables(): void
    {
        $this->pdo->exec("
            CREATE TABLE cities (
                id INTEGER PRIMARY KEY,
                name TEXT
            );
        ");

        $this->pdo->exec("
            CREATE TABLE contacts (
                id INTEGER PRIMARY KEY,
                name TEXT,
                first_name TEXT,
                email TEXT,
                street TEXT,
                zip_code TEXT,
                city_id INTEGER,
                FOREIGN KEY(city_id) REFERENCES cities(id)
            );
        ");

        $this->pdo->exec("
            CREATE TABLE groups (
                id INTEGER PRIMARY KEY,
                name TEXT
            );
        ");

        $this->pdo->exec("
            CREATE TABLE tags (
                id INTEGER PRIMARY KEY,
                name TEXT
            );
        ");

        $this->pdo->exec("
            CREATE TABLE group_contacts (
                contact_id INTEGER,
                group_id INTEGER,
                FOREIGN KEY(contact_id) REFERENCES contacts(id),
                FOREIGN KEY(group_id) REFERENCES groups(id)
            );
        ");

        $this->pdo->exec("
            CREATE TABLE contact_tags (
                contact_id INTEGER,
                tag_id INTEGER,
                FOREIGN KEY(contact_id) REFERENCES contacts(id),
                FOREIGN KEY(tag_id) REFERENCES tags(id)
            );
        ");

        $this->pdo->exec("
            CREATE TABLE group_inheritance (
                parent_group_id INTEGER,
                child_group_id INTEGER,
                FOREIGN KEY(parent_group_id) REFERENCES groups(id),
                FOREIGN KEY(child_group_id) REFERENCES groups(id)
            );
        ");
    }

    public function testCreateCity(): void
    {
        $city = new City($this->pdo);
        $savedCity = $city->save(['name' => 'Test City']);

        $this->assertNotNull($savedCity);
        $this->assertEquals('Test City', $savedCity['name']);
    }

    public function testFindCity(): void
    {
        $city = new City($this->pdo);
        $savedCity = $city->save(['name' => 'Test City']);
        $foundCity = $city->find($savedCity['id']);

        $this->assertNotNull($foundCity);
        $this->assertEquals('Test City', $foundCity['name']);
    }

    public function testUpdateCity(): void
    {
        $city = new City($this->pdo);
        $savedCity = $city->save(['name' => 'Old Name']);
        $updated = $city->update($savedCity['id'], ['name' => 'New Name']);
        $updatedCity = $city->find($savedCity['id']);

        $this->assertTrue($updated);
        $this->assertEquals('New Name', $updatedCity['name']);
    }

    public function testDeleteCity(): void
    {
        $city = new City($this->pdo);
        $savedCity = $city->save(['name' => 'Test City']);
        $deleted = $city->delete($savedCity['id']);
        $deletedCity = $city->find($savedCity['id']);

        $this->assertTrue($deleted);
        $this->assertNull($deletedCity);
    }

    public function testCityHasManyContacts(): void
    {
        $city = new City($this->pdo);
        $savedCity = $city->save(['name' => 'Test City']);

        $contact = new Contact($this->pdo);
        $savedContact1 = $contact->save(['name' => 'John Doe', 'city_id' => $savedCity['id']]);
        $savedContact2 = $contact->save(['name' => 'Jane Doe', 'city_id' => $savedCity['id']]);

        $contacts = $city->contacts($savedCity['id']);

        $this->assertCount(2, $contacts);
        $this->assertEquals('John Doe', $contacts[0]['name']);
        $this->assertEquals('Jane Doe', $contacts[1]['name']);
    }

    public function testContactBelongsToCity(): void
    {
        $city = new City($this->pdo);
        $savedCity = $city->save(['name' => 'Test City']);

        $contact = new Contact($this->pdo);
        $savedContact = $contact->save(['name' => 'John Doe', 'city_id' => $savedCity['id']]);

        $relatedCity = $contact->city($savedContact['id']);

        $this->assertNotNull($relatedCity);
        $this->assertEquals('Test City', $relatedCity['name']);
    }

    public function testContactBelongsToManyGroups(): void
    {
        $contact = new Contact($this->pdo);
        $savedContact = $contact->save(['name' => 'John Doe']);

        $group = new Group($this->pdo);
        $savedGroup1 = $group->save(['name' => 'Group 1']);
        $savedGroup2 = $group->save(['name' => 'Group 2']);

        $this->pdo->exec("INSERT INTO group_contacts (contact_id, group_id) VALUES ({$savedContact['id']}, {$savedGroup1['id']})");
        $this->pdo->exec("INSERT INTO group_contacts (contact_id, group_id) VALUES ({$savedContact['id']}, {$savedGroup2['id']})");

        $groups = $contact->groups($savedContact['id']);

        $this->assertCount(2, $groups);
        $this->assertEquals('Group 1', $groups[0]['name']);
        $this->assertEquals('Group 2', $groups[1]['name']);
    }

    public function testContactBelongsToManyTags(): void
    {
        $contact = new Contact($this->pdo);
        $savedContact = $contact->save(['name' => 'John Doe']);

        $tag = new Tag($this->pdo);
        $savedTag1 = $tag->save(['name' => 'Tag 1']);
        $savedTag2 = $tag->save(['name' => 'Tag 2']);

        $this->pdo->exec("INSERT INTO contact_tags (contact_id, tag_id) VALUES ({$savedContact['id']}, {$savedTag1['id']})");
        $this->pdo->exec("INSERT INTO contact_tags (contact_id, tag_id) VALUES ({$savedContact['id']}, {$savedTag2['id']})");

        $tags = $contact->tags($savedContact['id']);

        $this->assertCount(2, $tags);
        $this->assertEquals('Tag 1', $tags[0]['name']);
        $this->assertEquals('Tag 2', $tags[1]['name']);
    }
}
