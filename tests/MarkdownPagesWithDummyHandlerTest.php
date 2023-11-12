<?php

namespace Tests;

use Michalsn\CodeIgniterMarkdownPages\MarkdownPages;
use Michalsn\CodeIgniterMarkdownPages\Pages\Content;
use Michalsn\CodeIgniterMarkdownPages\Pages\Dir;
use Michalsn\CodeIgniterMarkdownPages\Pages\File;
use Michalsn\CodeIgniterMarkdownPages\Search\Result;
use Michalsn\CodeIgniterMarkdownPages\Search\Results;
use Myth\Collection\Collection;
use Tests\Support\Config\MarkdownPages as MarkdownPagesConfig;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class MarkdownPagesWithDummyHandlerTest extends TestCase
{
    private string $folderPath;
    private MarkdownPagesConfig $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->folderPath             = SUPPORTPATH . 'Pages';
        $this->config                 = config(MarkdownPagesConfig::class);
        $this->config->defaultHandler = 'dummy';
        $this->config->fileExtension  = 'html';
    }

    public function testMarkdownPages()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $this->assertInstanceOf(MarkdownPages::class, $markdownPages);
    }

    public function testDir()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $dir           = $markdownPages->dir('folder');

        $this->assertSame('Folder', $dir->getName());
        $this->assertSame('folder', $dir->getSlug());
        $this->assertSame('1_folder', $dir->getDirName());
        $this->assertInstanceOf(Collection::class, $dir->getFiles());
        $this->assertCount(1, $dir->getFiles());
        $this->assertInstanceOf(Dir::class, $dir);
    }

    public function testDirs()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $collection    = $markdownPages->dirs('folder');

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertCount(1, $collection);

        $dir = $collection->first();

        $this->assertSame('Folder', $dir->getName());
        $this->assertSame('folder', $dir->getSlug());
        $this->assertSame('1_folder', $dir->getDirName());
        $this->assertInstanceOf(Collection::class, $dir->getFiles());
        $this->assertCount(1, $dir->getFiles());
        $this->assertInstanceOf(Dir::class, $dir);
    }

    public function testFileWithDummyHandler()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $file          = $markdownPages->file('folder/surprise');

        $this->assertInstanceOf(File::class, $file);
        $this->assertSame('Surprise', $file->getName());
        $this->assertSame('surprise', $file->getSlug());
        $this->assertSame('1_folder', $file->getDirName());
        $this->assertSame('folder', $file->getDirNameSlug());

        $this->assertSame('folder/surprise', $file->urlPath());

        $rawContent = <<<'EOT'
            <h1>Surprise</h1>

            <p>Html extension</p>

            EOT;
        $this->assertSame($rawContent, $file->load());

        $content = $file->parse();
        $this->assertInstanceOf(Content::class, $content);
        $this->assertSame($rawContent, $content->getContent());
    }

    public function testSearch()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $results       = $markdownPages->search('extension');
        $this->assertInstanceOf(Results::class, $results);

        $this->assertCount(1, $results->getResults());

        $result = $results->getResults()->first();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(1, $result->getScore());

        $file = $result->getFile();
        $this->assertInstanceOf(File::class, $file);

        $this->assertSame('Surprise', $file->getName());
        $this->assertSame('folder/surprise', $file->urlPath());

        $content = $file->parse();
        $this->assertInstanceOf(Content::class, $content);

        $parsedContent = <<<'EOT'
            <h1>Surprise</h1>

            <p>Html extension</p>

            EOT;
        $this->assertSame($parsedContent, $content->getContent());

        $this->assertSame([], $content->getMeta());
        $this->assertNull($content->getMeta('invalid'));
    }
}
