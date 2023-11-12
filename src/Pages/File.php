<?php

namespace Michalsn\CodeIgniterMarkdownPages\Pages;

use Michalsn\CodeIgniterMarkdownPages\Exceptions\MarkdownPagesException;
use Mni\FrontYAML\Parser;

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
        protected Parser $parser
    ) {
        helper('inflector');

        $this->slug = $this->cleanup($fileName);

        $this->name = humanize($this->slug, '-');

        $paths             = explode('/', $dirName);
        $this->dirNameSlug = implode('/', array_map(fn ($path) => $this->cleanup($path), $paths));
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
            DIRECTORY_SEPARATOR,
            [
                $this->getBasePath(), $this->getDirName(), $this->getFileName(),
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
     * Parse content of the file.
     */
    public function parse(bool $parseMarkdown = true): Content
    {
        $rawContent = $this->load(true);

        $document = $this->parser->parse($rawContent, $parseMarkdown);

        return new Content($document->getContent(), $document->getYAML() ?? []);
    }

    /**
     * Search for query in the file content.
     */
    public function search(string $query, ?string $rawContent = null, array $metaKeys = []): int
    {
        $rawContent ??= $this->load(true);

        $document = $this->parser->parse($rawContent, false);
        $content  = new Content($document->getContent(), $document->getYAML() ?? []);

        $score = 0;
        $score += mb_substr_count($this->getName(), $query);
        $score += mb_substr_count($content->getContent(), $query);

        if ($metaKeys === []) {
            return $score;
        }

        foreach ($metaKeys as $metaKey) {
            if ($content->hasMetaKey($metaKey)) {
                $score += mb_substr_count(mb_strtolower((string) $content->getMeta($metaKey)), $query);
            }
        }

        return $score;
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
