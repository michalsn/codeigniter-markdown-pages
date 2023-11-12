<?php

namespace Michalsn\CodeIgniterMarkdownPages;

use Michalsn\CodeIgniterMarkdownPages\Config\MarkdownPages as MarkdownPagesConfig;
use Michalsn\CodeIgniterMarkdownPages\Enums\SearchField;
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
        $data = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folderPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $subPath  = $iterator->getSubPath();
            $fileName = $file->getFilename();

            if ($file->isDir()) {
                $folder = $subPath === '' ? $fileName : $subPath . '/' . $fileName;
            } else {
                $folder = $subPath;
            }

            if (! isset($data[$folder])) {
                $data[$folder] = new Dir($folder, $folderPath);
            }

            if ($file->isFile() && $file->getExtension() === $config->fileExtension) {
                $data[$folder]->addFile($fileName, $parser);
            }
        }

        // Sort
        $this->pages = new Collection(array_values($data));
        $this->pages = $this->pages->sort(static fn ($dir) => $dir->getDirName());
        $this->pages->each(static fn ($dir) => $dir->getFiles()->sort(static fn ($file) => $file->getFileName()));
    }

    public function depth(int|array $depth): static
    {
        $this->depth = $depth;

        return $this;
    }

    public function parent(string|array $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get dir based on value.
     */
    public function dir(string|array $value, SearchField $field = SearchField::SLUG): ?Dir
    {
        $dirs = $this->dirs($value, $field);

        if ($dirs->isEmpty()) {
            return null;
        }

        return $dirs->slice(0, 1)->first();
    }

    /**
     * Get dirs based on value.
     */
    public function dirs(string|array|null $value = null, SearchField $field = SearchField::SLUG): Collection
    {
        $depth  = $this->depth;
        $parent = $this->parent;

        if ($value === null && $depth === null && $parent === null) {
            return $this->pages;
        }

        $collection = $this->pages->filter(static function ($item) use ($value, $depth, $parent, $field) {
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

            if ($value === null) {
                return true;
            }

            if (is_array($value)) {
                return in_array($item->{$field->value}(), $value, true);
            }

            if (str_contains($value, '*')) {
                return str_starts_with((string) $item->{$field->value}(), rtrim($value, '*'));
            }

            return $item->{$field->value}() === $value;
        });

        // Reset options
        $this->resetOptions();

        return $collection;
    }

    /**
     * Get file based on value.
     */
    public function file(string $value, SearchField $field = SearchField::SLUG): ?File
    {
        $segments = explode('/', $value);

        if (count($segments) === 1) {
            $folder = '';
        } else {
            $value  = array_pop($segments);
            $folder = implode('/', $segments);
        }

        if (! $dir = $this->dir($folder)) {
            return null;
        }

        return $dir->getFiles()->find(static fn ($item) => $item->{$field->value}() === $value);
    }

    /**
     * Search through the files.
     */
    public function search(string $query, string|array|null $dirs = null, array $keys = []): Results
    {
        $search = new Results($query);

        foreach ($this->dirs($dirs)->items() as $dir) {
            foreach ($dir->getFiles()->items() as $file) {
                $score = 0;
                /** @var File $file */
                if ($content = $file->load()) {
                    $content = mb_strtolower($content);
                    // Search file name
                    $score += mb_substr_count(mb_strtolower($file->getName()), $query);
                    // Search content
                    $score += $file->search($query, $content, $keys);
                    if ($score > 0) {
                        $search->add(new Result($file, $score));
                    }
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
