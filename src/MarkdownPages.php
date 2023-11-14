<?php

namespace Michalsn\CodeIgniterMarkdownPages;

use Michalsn\CodeIgniterMarkdownPages\Config\MarkdownPages as MarkdownPagesConfig;
use Michalsn\CodeIgniterMarkdownPages\Exceptions\MarkdownPagesException;
use Michalsn\CodeIgniterMarkdownPages\Pages\Dir;
use Michalsn\CodeIgniterMarkdownPages\Pages\File;
use Michalsn\CodeIgniterMarkdownPages\Search\Result;
use Michalsn\CodeIgniterMarkdownPages\Search\Results;
use Mni\FrontYAML\Parser;
use Myth\Collection\Collection;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class MarkdownPages
{
    protected Collection $pages;
    protected int|array|null $depth     = null;
    protected string|array|null $parent = null;

    public function __construct(string $folderPath, protected MarkdownPagesConfig $config)
    {
        if (! file_exists($folderPath) || ! is_dir($folderPath)) {
            throw MarkdownPagesException::forIncorrectFolderPath();
        }

        // Parser
        $parser = new Parser(
            $config->yamlParser !== null ? new $config->yamlParser() : null,
            $config->markdownParser !== null ? new $config->markdownParser() : null
        );

        // Prepare folders and files
        $this->pages = new Collection([]);

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folderPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $subPath = $iterator->getSubPath();

            if ($file->isFile() && $subPath !== '') {
                continue;
            }

            $fileName = $file->getFilename();

            if ($file->isDir()) {
                $folder = $subPath === '' ? $fileName : $subPath . '/' . $fileName;
            } else {
                $folder = $subPath;
            }

            $this->pages->push(new Dir($folder, $folderPath, $config->fileExtension, $parser));
        }

        // Sort
        $this->pages = $this->pages->sort(static fn ($dir) => $dir->getDirName());
        $this->pages->each(static fn ($dir) => $dir->getFiles()->sort(static fn ($file) => $file->getFileName()));
    }

    /**
     * Prefilter folder depth.
     */
    public function depth(int|array $depth): static
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * Prefilter folder parent path.
     */
    public function parent(string|array $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get dir based on value.
     */
    public function dir(string|array $path): ?Dir
    {
        $dirs = $this->dirs($path);

        if ($dirs->isEmpty()) {
            return null;
        }

        return $dirs->slice(0, 1)->first();
    }

    /**
     * Get dirs based on value.
     */
    public function dirs(string|array|null $path = null): Collection
    {
        $depth  = $this->depth;
        $parent = $this->parent;

        if ($path === null && $depth === null && $parent === null) {
            return $this->pages;
        }

        $collection = $this->pages->filter(static function ($item) use ($path, $depth, $parent) {
            if ($depth !== null && (is_array($depth) ?
                ! in_array($item->getDepth(), $depth, true) :
                $item->getDepth() > $depth)) {
                return false;
            }

            if ($parent !== null) {
                if (is_array($parent) && ! in_array($item->getParent(), $parent, true)) {
                    return false;
                }

                if (is_string($parent)) {
                    if (str_contains($parent, '*')) {
                        if (! str_starts_with((string) $item->getParent(), rtrim($parent, '*'))) {
                            return false;
                        }
                    } elseif ($item->getParent() !== $parent) {
                        return false;
                    }
                }
            }

            if ($path === null) {
                return true;
            }

            if (is_array($path)) {
                return in_array($item->getPath(), $path, true);
            }

            if (str_contains($path, '*')) {
                return str_starts_with((string) $item->getPath(), rtrim($path, '*'));
            }

            return $item->getPath() === $path;
        });

        // Reset options
        $this->resetOptions();

        return $collection;
    }

    /**
     * Get file based on value.
     */
    public function file(string $path): ?File
    {
        $segments = explode('/', $path);

        if (count($segments) === 1) {
            $dirPath = '';
        } else {
            $path    = array_pop($segments);
            $dirPath = implode('/', $segments);
        }

        if (! $dir = $this->dir($dirPath)) {
            return null;
        }

        return $dir->getFiles()->find(static fn ($item) => $item->getSlug() === $path);
    }

    /**
     * Search through the files.
     */
    public function search(string $query, string|array|null $path = null, array $metaKeys = []): Results
    {
        $search = new Results($query);

        foreach ($this->dirs($path)->items() as $dir) {
            foreach ($dir->getFiles()->items() as $file) {
                // Search content
                $score = $file->search($query, $metaKeys);
                if ($score > 0) {
                    $search->getResults()->push(new Result($file, $score));
                }
            }
        }

        return $search->sortByScore();
    }

    /**
     * Reset search dirs option.
     */
    protected function resetOptions(): void
    {
        $this->depth  = null;
        $this->parent = null;
    }
}
