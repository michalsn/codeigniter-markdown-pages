<?php

namespace Michalsn\CodeIgniterMarkdownPages\Pages;

use DirectoryIterator;
use Mni\FrontYAML\Parser;
use Myth\Collection\Collection;

class Dir
{
    protected string $name;
    protected string $slug;
    protected int $depth;
    protected ?string $parent;
    protected array $children;
    protected Collection $files;

    public function __construct(
        protected string $dirName,
        protected string $basePath,
        protected string $fileExtension,
        protected Parser $parser
    ) {
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

        // Init files
        $this->files = new Collection([]);

        // Init children
        $this->children = [];

        $this->init();
    }

    /**
     * Determine file list and children folders.
     */
    private function init(): void
    {
        $directory = $this->dirName === '' ? $this->basePath : $this->basePath . DIRECTORY_SEPARATOR . $this->dirName;
        $iterator  = new DirectoryIterator($directory);

        foreach ($iterator as $file) {
            if ($file->isDot()) {
                continue;
            }

            if ($file->isDir()) {
                // Add child folder
                $this->children[] = $this->dirName . '/' . $file->getFilename();
            } elseif ($file->getExtension() === $this->fileExtension) {
                // Add file
                $this->files->push(new File(
                    $file->getFilename(),
                    $this->dirName,
                    $this->basePath,
                    $this->depth,
                    $this->parser
                ));
            }
        }

        // Prepare children
        if ($this->children !== []) {
            sort($this->children);
            $this->children = array_map(fn ($child) => implode('/', $this->cleanupArray(explode('/', (string) $child))), $this->children);
        }
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
     * Get parent slug. A string reference
     * to the parent folder.
     */
    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * Does dir has parent.
     */
    public function hasParent(): bool
    {
        return $this->parent !== null;
    }

    /**
     * Get children array slug. A string reference
     * to the children folders.
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Does dir have children.
     */
    public function hasChildren(): bool
    {
        return $this->children !== [];
    }

    /**
     * Get collection of pages for dir.
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    /**
     * Does dir have files.
     */
    public function hasFiles(): bool
    {
        return ! $this->files->isEmpty();
    }

    /**
     * Get dir name.
     */
    public function getDirName(): string
    {
        return $this->dirName;
    }

    /**
     * Cleanup name.
     */
    private function cleanup(string $name): string
    {
        return str_contains($name, '_') ? explode('_', $name)[1] : $name;
    }

    /**
     * Cleanup array.
     */
    private function cleanupArray(array $paths): array
    {
        return array_map(fn ($path) => $this->cleanup($path), $paths);
    }
}
