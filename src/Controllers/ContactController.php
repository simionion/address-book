<?php
declare(strict_types=1);

namespace Controllers;

use Models\City;
use Models\Contact;
use Models\Group;
use Models\Tag;
use Views\HtmlRenderer;

class ContactController
{
    private HtmlRenderer $htmlRenderer;

    public function __construct(HtmlRenderer $htmlRenderer)
    {
        $this->htmlRenderer = $htmlRenderer;
        $this->htmlRenderer
            ->withLayout('Views/layout/main.php')
            ->withSidebar('Views/contact/sidebar.php')
            ->withGlobals([
                'groups' => Group::all(),
                'tags' => Tag::all(),
                'groupsInUse' => Group::whereHas('contacts')->get(),
                'tagsInUse' => Tag::whereHas('contacts')->get()
            ]);
    }

    public function index(): void
    {
        $groupId = $_GET['group'] ?? 'all';
        $tagId = $_GET['tag'] ?? 'all';

        if ($groupId === 'all' && $tagId === 'all') {
            $contacts = Contact::with(['city', 'groups', 'tags'])->get();
        } elseif ($groupId !== 'all') {
            $group = Group::find($groupId);
            $parentGroupIds = array_map(
                static function ($group) {
                    return $group['id'];
                },
                $group->getAllParentGroups()
            );
            $groupIds = array_merge([$groupId], $parentGroupIds);

            $contacts = Contact::whereHas(
                'groups',
                static function ($query) use ($groupIds) {
                    $query->whereIn('groups.id', $groupIds);
                }
            )->with(['city', 'tags'])->get();
        } else {
            $contacts = Tag::find($tagId)->contacts()->with(['city', 'groups'])->get();
        }

        $this->htmlRenderer
            ->withContent('Views/contact/index.php')
            ->withGlobals([
                'contacts' => $contacts,
                'cities' => City::all()
            ])
            ->render();
    }

    public function show(int $id): void
    {
        $contact = Contact::with(['city', 'groups', 'tags'])->find($id);

        $this->htmlRenderer
            ->withContent('Views/contact/show.php')
            ->withGlobals(['contact' => $contact])
            ->render();
    }

    public function create(): void
    {
        $this->htmlRenderer
            ->withContent('Views/contact/form.php')
            ->withGlobals(['cities' => City::all()])
            ->render();
    }

    public function store(): void
    {
        $postData = $_POST;
        $contact = Contact::create($postData);

        if (!empty($postData['group_ids'])) {
            $contact->groups()->attach($postData['group_ids']);
        }

        if (!empty($postData['tag_ids'])) {
            $contact->tags()->attach($postData['tag_ids']);
        }

        header('Location: /contacts');
    }

    public function edit(int $id): void
    {
        $contact = Contact::with(['groups', 'tags'])->find($id) ?? new Contact();

        // Extract group_ids and tag_ids
        $contact->group_ids = $contact->groups->pluck('id')->toArray();
        $contact->tag_ids = $contact->tags->pluck('id')->toArray();

        $this->htmlRenderer
            ->withContent('Views/contact/form.php')
            ->withGlobals([
                'contact' => $contact,
                'cities' => City::all()
            ])
            ->render();
    }

    public function update(int $id): void
    {
        $postData = $_POST;
        $contact = Contact::find($id);
        $contact->update($postData);
        $contact->groups()->sync($postData['group_ids'] ?? []);
        $contact->tags()->sync($postData['tag_ids'] ?? []);

        header('Location: /contacts');
    }

    public function destroy(int $id): void
    {
        Contact::destroy($id);
        header('Location: /contacts');
    }
}
