<?php

namespace Michalsn\CodeIgniterMarkdownPages;

use FilesystemIterator;
use Michalsn\CodeIgniterMarkdownPages\Config\MarkdownPages as MarkdownPagesConfig;
use Michalsn\CodeIgniterMarkdownPages\Enums\SortField;
use Michalsn\CodeIgniterMarkdownPages\Exceptions\MarkdownPagesException;
use Michalsn\CodeIgniterMarkdownPages\Pages\Dir;
use Michalsn\CodeIgniterMarkdownPages\Pages\File;
use Michalsn\CodeIgniterMarkdownPages\Search\Result;
use Michalsn\CodeIgniterMarkdownPages\Search\Results;
use Myth\Collection\Collection;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class MarkdownPages
{
    protected Collection $pages;

    public function __construct(string $folderPath, protected MarkdownPagesConfig $config)
    {
        if (! file_exists($folderPath) || ! is_dir($folderPath)) {
            throw MarkdownPagesException::forIncorrectFolderPath();
        }

        if (! isset($config->handlers[$config->defaultHandler])) {
            throw MarkdownPagesException::forIncorrectHandler();
        }

        // Parser
        $parser = new $config->handlers[$config->defaultHandler]();

        // Prepare folders and files
        $data = [];

        $directoryIterator = new RecursiveDirectoryIterator($folderPath, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === $config->fileExtension) {
                $folder = $iterator->getSubPath();
                $file   = $file->getFilename();

                if (! isset($data[$folder])) {
                    $data[$folder] = new Dir($folder, $folderPath);
                }

                $data[$folder]->addFile($file, $parser);
            }
        }

        // Sort
        $this->pages = new Collection(array_values($data));
        $this->pages = $this->pages->sort(function ($dir) {
            return $dir->getDirName();
        });
        $this->pages->each(function ($dir) {
            return $dir->getFiles()->sort(function ($file) {
                return $file->getFileName();
            });
        });
    }

    /**
     * Get dir based on value.
     */
    public function dir(string|array $value, SortField $field = SortField::SLUG): ?Dir
    {
        return $this->pages->find(function($item) use ($value, $field) {
            if (is_array($value)) {
                return in_array($item->{$field->value}(), $value, true);
            }

            if (str_contains($value, '*')) {
                return str_starts_with($item->{$field->value}(), rtrim($value, '*'));
            }

            return $item->{$field->value}() === $value;
        });
    }

    /**
     * Get dirs based on value.
     */
    public function dirs(string|array $value = null, SortField $field = SortField::SLUG): Collection
    {
        if ($value === null) {
            return $this->pages;
        }

        return $this->pages->filter(function($item) use ($value, $field) {
            if (is_array($value)) {
                return in_array($item->{$field->value}(), $value, true);
            }

            if (str_contains($value, '*')) {
                return str_starts_with($item->{$field->value}(), rtrim($value, '*'));
            }

            return $item->{$field->value}() === $value;
        });
    }

    /**
     * Get file based on value.
     */
    public function file(string $value, SortField $field = SortField::SLUG): ?File
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

        return $dir->getFiles()->find(function($item) use ($value, $field) {
            return $item->{$field->value}() === $value;
        });
    }

    /**
     * Search through the files.
     */
    public function search(string $query): Results
    {
        $search = new Results($query);

        foreach ($this->pages->items() as $dir) {
            foreach ($dir->getFiles()->items() as $file) {
                if ($content = $file->load()) {
                    $content = mb_strtolower($content);
                    if (($score = mb_substr_count($content, $query)) > 0) {
                        $search->add(new Result($file, $score));
                    }
                }
            }
        }

        return $search->sortByScore();
    }
}
