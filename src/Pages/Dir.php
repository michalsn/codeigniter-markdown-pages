<?php

namespace Michalsn\CodeIgniterMarkdownPages\Pages;

use Michalsn\CodeIgniterMarkdownPages\Interfaces\HandlerInterface;
use Myth\Collection\Collection;

class Dir
{
    protected string $name;
    protected string $slug;
    protected int $depth;
    protected ?string $parent;
    protected Collection $files;

    public function __construct(protected string $dirName, protected string $basePath)
    {
        helper('inflector');

        $paths = explode('/', $dirName);

        // Set depth
        $this->depth = $dirName === '' ? 0 : count($paths);

        // Set parent
        $this->parent = $this->depth > 0 ? implode('/', $this->cleanupArray(array_slice($paths, 0, -1))) : null;

        // Set slug
        $this->slug = implode('/', $this->cleanupArray($paths));

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
     * Get parent slug/path... not really,
     * just a string "reference" to parent.
     */
    public function getParent(): ?string
    {
        return $this->parent;
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

    /**
     * Cleanup array.
     */
    protected function cleanupArray(array $paths): array
    {
        return array_map(fn ($path) => $this->cleanup($path), $paths);
    }
}
