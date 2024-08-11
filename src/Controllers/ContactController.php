<?php
declare(strict_types=1);

namespace Controllers;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use JsonException;
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
            $contacts = $this->contactModel->getContactsByGroupWithRelations((int)$groupId);
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

    public function show(int $id): void
    {
        $contact = $this->contactModel->getContactWithRelations($id);

        $this->htmlRenderer
            ->withContent('Views/contact/show.php')
            ->withGlobals(['contact' => $contact])
            ->render();
    }

    public function create(): void
    {
        $this->htmlRenderer
            ->withContent('Views/contact/form.php')
            ->withGlobals([
                'cities' => $this->cityModel->all(),
                'groups' => $this->groupModel->all(),
                'tags' => $this->tagModel->all()
            ])
            ->render();
    }

    /**
     * @throws Exception
     */
    public function store(): void
    {
        $postData = $_POST;
        $contact = $this->contactModel->save($postData);

        if (!empty($postData['group_ids'])) {
            $this->contactModel->syncGroups($contact['id'], $postData['group_ids']);
        }

        if (!empty($postData['tag_ids'])) {
            $this->contactModel->syncTags($contact['id'], $postData['tag_ids']);
        }

        header('Location: /contacts');
    }

    public function edit(int $id): void
    {
        $contact = $this->contactModel->getContactWithRelations($id);

        $contact['group_ids'] = array_column($contact['groups'], 'id');
        $contact['tag_ids'] = array_column($contact['tags'], 'id');

        $this->htmlRenderer
            ->withContent('Views/contact/form.php')
            ->withGlobals([
                'contact' => $contact,
                'cities' => $this->cityModel->all(),
                'groups' => $this->groupModel->all(),
                'tags' => $this->tagModel->all()
            ])
            ->render();
    }

    /**
     * @throws Exception
     */
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

    /**
     * @throws JsonException
     */
    #[NoReturn] public function export(): void
    {
        $format = $_GET['format'] ?? 'json';
        $contacts = $this->contactModel->getAllWithRelations();

        switch ($format) {
            case 'xml':
                $this->exportXml($contacts);
            case 'json':
            default:
                $this->exportJson($contacts);
        }
    }

    #[NoReturn] private function exportXml(array $contacts): void
    {
        header('Content-Type: application/xml');
        $xml = new SimpleXMLElement('<contacts/>');

        foreach ($contacts as $contact) {
            $contactNode = $xml->addChild('contact');
            if ($contactNode !== null) {
                $this->arrayToXml($contact, $contactNode);
            }
        }

        echo $xml->asXML();
        exit;
    }

    private function arrayToXml(array $data, SimpleXMLElement $xmlElement): void
    {
        foreach ($data as $key => $value) {
            $key = is_int($key) ? 'item' . $key : $key;

            if (is_array($value)) {
                $subnode = $xmlElement->addChild($key);
                if ($subnode !== null) {
                    $this->arrayToXml($value, $subnode);
                }
            } else {
                $xmlElement->addChild($key, htmlspecialchars((string)($value ?? '')));
            }
        }
    }

    /**
     * @throws JsonException
     */
    #[NoReturn] private function exportJson(array $contacts): void
    {
        header('Content-Type: application/json');
        echo json_encode($contacts, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        exit;
    }
}
