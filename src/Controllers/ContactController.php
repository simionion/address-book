<?php
declare(strict_types=1);

namespace Controllers;

use Models\City;
use Models\Contact;
use Models\Group;
use Models\Tag;
use SimpleXMLElement;
use Views\HtmlRenderer;

class ContactController
{
    private HtmlRenderer $htmlRenderer;
    private City $cityModel;
    private Contact $contactModel;
    private Group $groupModel;
    private Tag $tagModel;

    public function __construct(HtmlRenderer $htmlRenderer, City $cityModel, Contact $contactModel, Group $groupModel, Tag $tagModel)
    {
        $this->htmlRenderer = $htmlRenderer;
        $this->cityModel = $cityModel;
        $this->contactModel = $contactModel;
        $this->groupModel = $groupModel;
        $this->tagModel = $tagModel;

        $this->htmlRenderer
            ->withLayout('Views/layout/main.php')
            ->withSidebar('Views/contact/sidebar.php')
            ->withGlobals([
                'groups' => $this->groupModel->all(),
                'tags' => $this->tagModel->all(),
                'groupsInUse' => $this->groupModel->whereHasContacts(),
                'tagsInUse' => $this->tagModel->whereHasContacts()
            ]);
    }

    public function index(): void
    {
        $groupId = $_GET['group'] ?? 'all';
        $tagId = $_GET['tag'] ?? 'all';

        if ($groupId === 'all' && $tagId === 'all') {
            $contacts = $this->contactModel->getAllWithRelations();
        } elseif ($groupId !== 'all') {
            $contacts = $this->getContactsByGroupId((int)$groupId);
        } else {
            $contacts = $this->contactModel->getByTagIdsWithRelations([(int)$tagId]);
        }

        $this->htmlRenderer
            ->withContent('Views/contact/index.php')
            ->withGlobals([
                'contacts' => $contacts,
                'cities' => $this->cityModel->all(),
            ])
            ->render();
    }

    private function getContactsByGroupId(int $groupId): array
    {
        $group = $this->groupModel->find($groupId);
        if (!$group) {
            return [];
        }

        $parentGroupIds = array_column($this->groupModel->parentGroups($groupId), 'id');
        $groupIds = array_merge([$groupId], $parentGroupIds);

        return $this->contactModel->getByGroupIdsWithRelations($groupIds);
    }

    public function show(int $id): void
    {
        $contact = $this->contactModel->with(['city', 'groups', 'tags'])->find($id);

        $this->htmlRenderer
            ->withContent('Views/contact/show.php')
            ->withGlobals(['contact' => $contact])
            ->render();
    }

    public function create(): void
    {
        $this->htmlRenderer
            ->withContent('Views/contact/form.php')
            ->withGlobals(['cities' => $this->cityModel->all()])
            ->render();
    }

    public function store(): void
    {
        $postData = $_POST;
        $contact = $this->contactModel->save($postData);

        if (!empty($postData['group_ids'])) {
            $this->contactModel->attachGroups($contact['id'], $postData['group_ids']);
        }

        if (!empty($postData['tag_ids'])) {
            $this->contactModel->attachTags($contact['id'], $postData['tag_ids']);
        }

        header('Location: /contacts');
    }

    public function edit(int $id): void
    {
        $contact = $this->contactModel->with(['groups', 'tags'])->find($id);

        // Extract group_ids and tag_ids
        $contact['group_ids'] = array_column($contact['groups'], 'id');
        $contact['tag_ids'] = array_column($contact['tags'], 'id');

        $this->htmlRenderer
            ->withContent('Views/contact/form.php')
            ->withGlobals([
                'contact' => $contact,
                'cities' => $this->cityModel->all()
            ])
            ->render();
    }

    public function update(int $id): void
    {
        $postData = $_POST;
        $this->contactModel->update($id, $postData);
        $this->contactModel->syncGroups($id, $postData['group_ids'] ?? []);
        $this->contactModel->syncTags($id, $postData['tag_ids'] ?? []);

        header('Location: /contacts');
    }

    public function destroy(int $id): void
    {
        $this->contactModel->delete($id);
        header('Location: /contacts');
    }

    public function export(): void
    {
        $format = $_GET['format'] ?? 'json';
        $contacts = $this->contactModel->with(['city', 'groups', 'tags'])->all();

        switch ($format) {
            case 'xml':
                $this->exportXml($contacts);
                break;
            case 'json':
            default:
                $this->exportJson($contacts);
                break;
        }
    }

    private function exportXml(array $contacts): void
    {
        header('Content-Type: application/xml');
        $xml = new SimpleXMLElement('<contacts/>');

        foreach ($contacts as $contact) {
            $contactNode = $xml->addChild('contact');
            $this->arrayToXml($contact, $contactNode);
        }

        echo $xml->asXML();
        exit;
    }

    private function arrayToXml(array $data, SimpleXMLElement $xmlElement): void
    {
        foreach ($data as $key => $value) {
            // Ensure the key is a valid string for XML element names
            $key = is_int($key) ? 'item' . $key : $key;

            if (is_array($value)) {
                $subnode = $xmlElement->addChild($key);
                $this->arrayToXml($value, $subnode);
            } else {
                // Handle possible null values by converting them to empty strings
                $xmlElement->addChild($key, htmlspecialchars((string)($value ?? '')));
            }
        }
    }

    private function exportJson(array $contacts): void
    {
        header('Content-Type: application/json');
        echo json_encode($contacts, JSON_PRETTY_PRINT);
        exit;
    }
}
