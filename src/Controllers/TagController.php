<?php
declare(strict_types=1);

namespace Controllers;

use Models\Tag;
use Views\HtmlRenderer;

class TagController
{
    private HtmlRenderer $htmlRenderer;
    private Tag $tagModel;

    public function __construct(HtmlRenderer $htmlRenderer, Tag $tagModel)
    {
        $this->htmlRenderer = $htmlRenderer;
        $this->tagModel = $tagModel;
        $this->htmlRenderer->withLayout('Views/layout/main.php');
    }

    public function index(): void
    {
        $tags = $this->tagModel->all();
        $this->htmlRenderer
            ->withContent('Views/tag/index.php')
            ->withGlobals(['tags' => $tags])
            ->render();
    }

    public function create(): void
    {
        $this->htmlRenderer
            ->withContent('Views/tag/form.php')
            ->render();
    }

    public function store(): void
    {
        $this->tagModel->save($_POST);
        header('Location: /tags');
    }

    public function edit(int $id): void
    {
        $tag = $this->tagModel->find($id);
        $this->htmlRenderer
            ->withContent('Views/tag/form.php')
            ->withGlobals(['tag' => $tag])
            ->render();
    }

    public function update(int $id): void
    {
        $this->tagModel->update($id, $_POST);
        header('Location: /tags');
    }

    public function destroy(int $id): void
    {
        $this->tagModel->delete($id);
        header('Location: /tags');
    }
}
