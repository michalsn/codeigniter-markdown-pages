<?php

namespace Michalsn\CodeIgniterMarkdownPages\Pages;

use Michalsn\CodeIgniterMarkdownPages\Interfaces\HandlerInterface;
use Myth\Collection\Collection;

class Dir
{
    protected string $name;
    protected string $slug;
    protected int $depth;
    protected Collection $files;

    public function __construct(protected string $dirName, protected string $basePath)
    {
        helper('inflector');

        $paths = explode('/', $dirName);

        // Set depth
        $this->depth = $dirName === '' ? 0 : count($paths);

        // Set slug
        $this->slug = implode('/', array_map(fn ($path) => $this->cleanup($path), $paths));

        // Set name
        $this->name = humanize($this->cleanup(end($paths)), '-');

        // Set pages
        $this->files = new Collection([]);
    }

    /**
     * Add file to the collection.
     */
    public function addFile(string $page, HandlerInterface $parser): static
    {
        $this->files->push(new File(
            $page,
            $this->dirName,
            $this->basePath,
            $this->depth,
            $parser
        ));

        return $this;
    }

    /**
     * Get dir title.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get dir slug.
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
     * Get collection of pages for dir.
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    /**
     * Get dir name.
     */
    public function getDirName(): string
    {
        return $this->dirName;
    }

    /**
     * Get base path.
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Cleanup name.
     */
    protected function cleanup(string $name): string
    {
        return str_contains($name, '_') ? explode('_', $name)[1] : $name;
    }
}
