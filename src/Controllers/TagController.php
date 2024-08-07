<?php
declare(strict_types=1);
namespace Controllers;

use Models\Tag;
use Views\HtmlRenderer;

class TagController
{
    private HtmlRenderer $htmlRenderer;

    public function __construct(HtmlRenderer $htmlRenderer)
    {
        $this->htmlRenderer = $htmlRenderer;
        $this->htmlRenderer->withLayout('Views/layout/main.php');
    }

    public function index(): void
    {
        $tags = Tag::all();
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
        Tag::create($_POST);
        header('Location: /tags');
    }

    public function edit(int $id): void
    {
        $tag = Tag::find($id);
        $this->htmlRenderer
            ->withContent('Views/tag/form.php')
            ->withGlobals(['tag' => $tag])
            ->render();
    }

    public function update(int $id): void
    {
        $tag = Tag::find($id);
        $tag->update($_POST);
        header('Location: /tags');
    }

    public function destroy(int $id): void
    {
        $tag = Tag::find($id);
        $tag->delete();
        header('Location: /tags');
    }
}
