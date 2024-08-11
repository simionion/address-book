<?php
declare(strict_types=1);

namespace Controllers;

use Exception;
use Models\Group;
use Views\HtmlRenderer;

class GroupController
{
    private HtmlRenderer $htmlRenderer;
    private Group $groupModel;

    public function __construct(HtmlRenderer $htmlRenderer, Group $groupModel)
    {
        $this->htmlRenderer = $htmlRenderer;
        $this->groupModel = $groupModel;
        $this->htmlRenderer->withLayout('Views/layout/main.php');
    }

    public function index(): void
    {
        $groups = $this->groupModel->getAllWithChildGroups();
        $this->htmlRenderer
            ->withContent('Views/group/index.php')
            ->withGlobals(['groups' => $groups])
            ->render();
    }

    public function create(): void
    {
        $groups = $this->groupModel->all();
        $this->htmlRenderer
            ->withContent('Views/group/form.php')
            ->withGlobals(['groups' => $groups])
            ->render();
    }

    /**
     * @throws Exception
     */
    public function store(): void
    {
        $group = $this->groupModel->save($_POST);

        if (!empty($_POST['parent_group_ids']) || !empty($_POST['child_group_ids'])) {
            $this->syncGroupRelations($group['id'], $_POST['parent_group_ids'] ?? [], $_POST['child_group_ids'] ?? []);
        }

        header('Location: /groups');
    }

    public function edit(int $id): void
    {
        $group = $this->groupModel->getGroupWithRelations($id);

        $groups = $this->groupModel->all();

        $this->htmlRenderer
            ->withContent('Views/group/form.php')
            ->withGlobals([
                'group' => $group,
                'groups' => $groups,
            ])
            ->render();
    }

    /**
     * @throws Exception
     */
    public function update(int $id): void
    {
        $this->groupModel->update($id, $_POST);

        $this->syncGroupRelations($id, $_POST['parent_group_ids'] ?? [], $_POST['child_group_ids'] ?? []);

        header('Location: /groups');
    }

    public function destroy(int $id): void
    {
        $this->groupModel->delete($id);
        header('Location: /groups');
    }

    /**
     * @throws Exception
     */
    private function syncGroupRelations(int $groupId, array $parentGroupIds, array $childGroupIds): void
    {
        $this->groupModel->syncGroupRelations($groupId, $parentGroupIds, $childGroupIds);
    }
}
