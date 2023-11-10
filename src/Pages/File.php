<?php

namespace Michalsn\CodeIgniterMarkdownPages\Pages;

use Michalsn\CodeIgniterMarkdownPages\Exceptions\MarkdownPagesException;
use Michalsn\CodeIgniterMarkdownPages\Interfaces\HandlerInterface;

class File
{
    protected string $name;
    protected string $slug;
    protected string $dirNameSlug;

    public function __construct(
        protected string $fileName,
        protected string $dirName,
        protected string $basePath,
        protected int $depth,
        protected HandlerInterface $parser)
    {
        helper('inflector');

        $this->slug = $this->cleanup($fileName);

        $this->name = humanize($this->slug, '-');

        $paths = explode('/', $dirName);
        $this->dirNameSlug = implode('/', array_map(function ($path) {
            return $this->cleanup($path);
        }, $paths));
    }

    /**
     * Get file name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get file slug.
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Get dir depth.
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * Get real file name with extension.
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * Get dir name.
     */
    public function getDirName(): string
    {
        return $this->dirName;
    }

    /**
     * Get dir name slug.
     */
    public function getDirNameSlug(): string
    {
        return $this->dirNameSlug;
    }

    /**
     * Get base path.
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Get url path for file.
     */
    public function urlPath(): string
    {
        return implode('/', [$this->getDirNameSlug(), $this->getSlug()]);
    }

    /**
     * Load content of the file.
     */
    public function load(bool $throw = false): ?string
    {
        $path = implode(
            DIRECTORY_SEPARATOR, [
                $this->getBasePath(), $this->getDirName(), $this->getFileName()
            ]
        );

        if (! file_exists($path)) {
            if ($throw) {
                throw MarkdownPagesException::forFileDoesNotExist($path);
            }

            return null;
        }

        return file_get_contents($path);
    }

    /**
     * Render content of the file.
     */
    public function render(): string
    {
        $content = $this->load(true);

        return $this->parser->parse($content);
    }

    /**
     * Cleanup name.
     */
    protected function cleanup(string $name): string
    {
        $name = pathinfo($name, PATHINFO_FILENAME);

        return str_contains($name, '_') ? explode('_', $name)[1] : $name;
    }
}
