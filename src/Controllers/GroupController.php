<?php
declare(strict_types=1);

namespace Controllers;

use Models\Group;
use Views\HtmlRenderer;

class GroupController
{
    private HtmlRenderer $htmlRenderer;

    public function __construct(HtmlRenderer $htmlRenderer)
    {
        $this->htmlRenderer = $htmlRenderer;
        $this->htmlRenderer->withLayout('Views/layout/main.php');
    }

    public function index(): void
    {
        $groups = Group::with('childGroups')->get();

        $this->htmlRenderer
            ->withContent('Views/group/index.php')
            ->withGlobals(['groups' => $groups])
            ->render();
    }

    public function create(): void
    {
        $this->htmlRenderer
            ->withContent('Views/group/form.php')
            ->withGlobals(['groups' => Group::all()])
            ->render();
    }

    public function store(): void
    {
        $group = Group::create($_POST);

        if (!empty($_POST['parent_group_ids'])) {
            $group->parentGroups()->attach($_POST['parent_group_ids']);
        }

        if (!empty($_POST['child_group_ids'])) {
            $group->childGroups()->attach($_POST['child_group_ids']);
        }

        header('Location: /groups');
    }

    public function edit(int $id): void
    {
        $group = Group::with(['parentGroups', 'childGroups'])->find($id);

        $this->htmlRenderer
            ->withContent('Views/group/form.php')
            ->withGlobals([
                'group' => $group,
                'groups' => Group::all()
            ])
            ->render();
    }

    public function update(int $id): void
    {
        $group = Group::find($id);
        $group->update($_POST);

        $group->parentGroups()->sync($_POST['parent_group_ids'] ?? []);
        $group->childGroups()->sync($_POST['child_group_ids'] ?? []);

        header('Location: /groups');
    }

    public function destroy(int $id): void
    {
        Group::destroy($id);
        header('Location: /groups');
    }
}

?>
