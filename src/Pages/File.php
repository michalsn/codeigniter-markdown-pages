<?php

namespace Michalsn\CodeIgniterMarkdownPages\Pages;

use Michalsn\CodeIgniterMarkdownPages\Exceptions\MarkdownPagesException;
use Mni\FrontYAML\Parser;

class File
{
    protected string $name;
    protected string $slug;
    protected string $path;
    protected string $dirNamePath;
    protected ?Content $content = null;

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
        $this->dirNamePath = implode('/', array_map(fn ($path) => $this->cleanup($path), $paths));
        $this->path        = implode('/', [$this->dirNamePath, $this->slug]);
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
     * Get path.
     */
    public function getPath(): string
    {
        return $this->path;
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
     * Get dir name path.
     */
    public function getDirNamePath(): string
    {
        return $this->dirNamePath;
    }

    /**
     * Load content of the file.
     */
    public function load(bool $throw = false): ?string
    {
        $path = implode(
            DIRECTORY_SEPARATOR,
            [
                $this->basePath, $this->getDirName(), $this->getFileName(),
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
        if ($this->content === null) {
            $rawContent = $this->load(true);

            $document = $this->parser->parse($rawContent, $parseMarkdown);

            $this->content = new Content($document->getContent(), $document->getYAML() ?? []);
        }

        return $this->content;
    }

    /**
     * Search for query in the file content.
     */
    public function search(string $query, array $metaKeys = []): int
    {
        $rawContent = mb_strtolower($this->load(true));

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
    private function cleanup(string $name): string
    {
        $name = pathinfo($name, PATHINFO_FILENAME);

        return str_contains($name, '_') ? explode('_', $name)[1] : $name;
    }
}
