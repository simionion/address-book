<?php
declare(strict_types=1);

namespace Views;
use RuntimeException;

class HtmlRenderer
{
    private string $layout;
    private string $content;
    private array $globals = [];

    public function withLayout(string $layout): self
    {
        chdir(__DIR__.'/../'); //Allow root relative paths
        $this->fileExistsOrThrow($layout);
        $this->layout = $layout;
        return $this;
    }



    public function withContent(string $content): self
    {
        chdir(__DIR__.'/../');
        $this->fileExistsOrThrow($content);
        $this->content = $content;
        return $this;
    }

    public function withGlobals(array $globals): self
    {
        $this->globals = $globals;
        return $this;
    }


    public function render(): void
    {

        foreach ($this->globals as $key => $value) {
            $$key = $value; // or extract
        }

        if (isset($this->content)) {
            $__content = $this->content;
        }

        if (isset($this->layout)) {
            require $this->layout;
        } else {
            echo "No layout set";
        }
    }

     public function fileExistsOrThrow(string $file): void
    {
        if (!file_exists($file)) {
            throw new RuntimeException("File {$file} not found");
        }
    }
}
