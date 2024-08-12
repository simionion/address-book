<?php
declare(strict_types=1);

use Models\City;
use Models\Contact;
use Models\Group;
use Models\Tag;
use PHPUnit\Framework\TestCase;

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
            CREATE TABLE groups_table (
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
                FOREIGN KEY(group_id) REFERENCES groups_table(id)
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
                FOREIGN KEY(parent_group_id) REFERENCES groups_table(id),
                FOREIGN KEY(child_group_id) REFERENCES groups_table(id)
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

    public function testGetAllWithRelations(): void
    {
        $city = new City($this->pdo);
        $savedCity = $city->save(['name' => 'Test City']);

        $group = new Group($this->pdo);
        $savedGroup = $group->save(['name' => 'Test Group']);

        $tag = new Tag($this->pdo);
        $savedTag = $tag->save(['name' => 'Test Tag']);

        $contact = new Contact($this->pdo);
        $savedContact = $contact->save([
            'name' => 'John Doe',
            'first_name' => 'John',
            'email' => 'john@example.com',
            'street' => '123 Main St',
            'zip_code' => '12345',
            'city_id' => $savedCity['id'],
        ]);

        $contact->attachGroups($savedContact['id'], [$savedGroup['id']]);
        $contact->attachTags($savedContact['id'], [$savedTag['id']]);

        $contacts = $contact->getAllWithRelations();

        $this->assertCount(1, $contacts);
        $this->assertEquals('John Doe', $contacts[0]['name']);
        $this->assertEquals('Test City', $contacts[0]['city_name']);
        $this->assertEquals('Test Group', $contacts[0]['groups'][0]['name']);
        $this->assertEquals('Test Tag', $contacts[0]['tags'][0]['name']);
    }

    public function testGetByTagIdsWithRelations(): void
    {
        $tag = new Tag($this->pdo);
        $savedTag = $tag->save(['name' => 'Test Tag']);

        $contact = new Contact($this->pdo);
        $savedContact = $contact->save([
            'name' => 'John Doe',
            'first_name' => 'John',
            'email' => 'john@example.com',
            'street' => '123 Main St',
            'zip_code' => '12345',
        ]);

        $contact->attachTags($savedContact['id'], [$savedTag['id']]);

        $contacts = $contact->getByTagIdsWithRelations([$savedTag['id']]);

        $this->assertCount(1, $contacts);
        $this->assertEquals('John Doe', $contacts[0]['name']);
        $this->assertEquals('Test Tag', $contacts[0]['tags'][0]['name']);
    }

    public function testGetContactsByGroupWithRelations(): void
    {
        $parentGroup = new Group($this->pdo);
        $savedParentGroup = $parentGroup->save(['name' => 'Parent Group']);

        $childGroup = new Group($this->pdo);
        $savedChildGroup = $childGroup->save(['name' => 'Child Group']);

        // Insert the parent-child relationship (childGroup -> parentGroup)
        $this->pdo->exec("INSERT INTO group_inheritance (parent_group_id, child_group_id) VALUES ({$savedParentGroup['id']}, {$savedChildGroup['id']})");

        $contact = new Contact($this->pdo);
        $savedContact = $contact->save([
            'name' => 'John Doe',
            'first_name' => 'John',
            'email' => 'john@example.com',
            'street' => '123 Main St',
            'zip_code' => '12345',
        ]);

        // Attach the contact to the child group
        $contact->attachGroups($savedContact['id'], [$savedChildGroup['id']]);

        // Retrieve contacts by the child group, which should also consider its parent group
        $contacts = $contact->getContactsByGroupWithRelations($savedChildGroup['id']);

        $this->assertCount(1, $contacts);
        $this->assertEquals('John Doe', $contacts[0]['name']);
        $this->assertEquals('Child Group', $contacts[0]['groups'][0]['name']);
    }

    public function testSyncGroups(): void
    {
        $contact = new Contact($this->pdo);
        $savedContact = $contact->save([
            'name' => 'John Doe',
            'first_name' => 'John',
            'email' => 'john@example.com',
            'street' => '123 Main St',
            'zip_code' => '12345',
        ]);

        $group = new Group($this->pdo);
        $savedGroup1 = $group->save(['name' => 'Group 1']);
        $savedGroup2 = $group->save(['name' => 'Group 2']);

        $contact->syncGroups($savedContact['id'], [$savedGroup1['id'], $savedGroup2['id']]);

        $groups = $contact->getContactWithRelations($savedContact['id'])['groups'];

        $this->assertCount(2, $groups);
        $this->assertEquals('Group 1', $groups[0]['name']);
        $this->assertEquals('Group 2', $groups[1]['name']);
    }

    public function testSyncTags(): void
    {
        $contact = new Contact($this->pdo);
        $savedContact = $contact->save([
            'name' => 'John Doe',
            'first_name' => 'John',
            'email' => 'john@example.com',
            'street' => '123 Main St',
            'zip_code' => '12345',
        ]);

        $tag = new Tag($this->pdo);
        $savedTag1 = $tag->save(['name' => 'Tag 1']);
        $savedTag2 = $tag->save(['name' => 'Tag 2']);

        $contact->syncTags($savedContact['id'], [$savedTag1['id'], $savedTag2['id']]);

        $tags = $contact->getContactWithRelations($savedContact['id'])['tags'];

        $this->assertCount(2, $tags);
        $this->assertEquals('Tag 1', $tags[0]['name']);
        $this->assertEquals('Tag 2', $tags[1]['name']);
    }
}
