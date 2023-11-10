<?php

namespace Tests;

use Michalsn\CodeIgniterMarkdownPages\Exceptions\MarkdownPagesException;
use Michalsn\CodeIgniterMarkdownPages\MarkdownPages;
use Michalsn\CodeIgniterMarkdownPages\Pages\Dir;
use Michalsn\CodeIgniterMarkdownPages\Pages\File;
use Michalsn\CodeIgniterMarkdownPages\Search\Results;
use Myth\Collection\Collection;
use Tests\Support\Config\MarkdownPages as MarkdownPagesConfig;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class MarkdownPagesTest extends TestCase
{
    private string $folderPath;
    private MarkdownPagesConfig $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->folderPath = SUPPORTPATH . 'Pages';
        $this->config     = config(MarkdownPagesConfig::class);
    }

    public function testMarkdownPages()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $this->assertInstanceOf(MarkdownPages::class, $markdownPages);
    }

    public function testMarkdownPagesIncorrectFolderPathException()
    {
        $this->expectException(MarkdownPagesException::class);
        $this->expectExceptionMessage('The $folderPath provided to the constructor is incorrect or not a folder.');

        new MarkdownPages(SUPPORTPATH . 'incorrect', $this->config);
    }

    public function testMarkdownPagesIncorrectHandlerException()
    {
        $this->expectException(MarkdownPagesException::class);
        $this->expectExceptionMessage('This markdown handler is incorrect.');

        $this->config->defaultHandler = 'incorrect';

        new MarkdownPages($this->folderPath, $this->config);
    }

    public function testDir()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $dir           = $markdownPages->dir('folder');

        $this->assertInstanceOf(Dir::class, $dir);

        $this->assertSame('Folder', $dir->getName());
        $this->assertSame('folder', $dir->getSlug());
        $this->assertSame('1_folder', $dir->getDirName());
        $this->assertInstanceOf(Collection::class, $dir->getFiles());
        $this->assertCount(3, $dir->getFiles());
    }

    public function testDirDoesNotExist()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $dir           = $markdownPages->dir('incorrect');

        $this->assertNull($dir);
    }

    public function testDirWithArray()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $dir           = $markdownPages->dir(['folder']);

        $this->assertInstanceOf(Dir::class, $dir);

        $this->assertSame('Folder', $dir->getName());
        $this->assertSame('folder', $dir->getSlug());
        $this->assertSame('1_folder', $dir->getDirName());
        $this->assertInstanceOf(Collection::class, $dir->getFiles());
        $this->assertCount(3, $dir->getFiles());
    }

    public function testDirWithStringSearch()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $dir           = $markdownPages->dir('another*');

        $this->assertInstanceOf(Dir::class, $dir);

        $this->assertSame('Another Best One', $dir->getName());
        $this->assertSame('another-best-one', $dir->getSlug());
        $this->assertSame('another-best-one', $dir->getDirName());
        $this->assertInstanceOf(Collection::class, $dir->getFiles());
        $this->assertCount(3, $dir->getFiles());
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
        $this->assertCount(3, $dir->getFiles());
        $this->assertInstanceOf(Dir::class, $dir);
    }

    public function testDirsWithNull()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $collection    = $markdownPages->dirs();

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertCount(7, $collection);

        $this->assertSame('', $collection->first()->getSlug());
        $this->assertSame('folder', $collection->next()->getSlug());
        $this->assertSame('x-files/danna-scully', $collection->next()->getSlug());
        $this->assertSame('x-files/cigarette-smoking-man', $collection->next()->getSlug());
        $this->assertSame('x-files/fox-mulder', $collection->next()->getSlug());
        $this->assertSame('another-best-one', $collection->next()->getSlug());
        $this->assertSame('another-one', $collection->next()->getSlug());
    }

    public function testDirsWhenNothingFound()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $collection    = $markdownPages->dirs('incorrect*');

        $this->assertTrue($collection->isEmpty());
    }

    public function testDirsWithArray()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $collection    = $markdownPages->dirs(['folder', 'another-one']);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertCount(2, $collection);

        $dir = $collection->first();
        $this->assertInstanceOf(Dir::class, $dir);

        $this->assertSame('Folder', $dir->getName());
        $this->assertSame('folder', $dir->getSlug());
        $this->assertSame('1_folder', $dir->getDirName());
        $this->assertInstanceOf(Collection::class, $dir->getFiles());
        $this->assertCount(3, $dir->getFiles());

        $dir = $collection->last();
        $this->assertInstanceOf(Dir::class, $dir);

        $this->assertSame('Another One', $dir->getName());
        $this->assertSame('another-one', $dir->getSlug());
        $this->assertSame('another-one', $dir->getDirName());
        $this->assertInstanceOf(Collection::class, $dir->getFiles());
        $this->assertCount(2, $dir->getFiles());
    }

    public function testDirsWithStringSearch()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $collection    = $markdownPages->dirs('another*');

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertCount(2, $collection);

        $dir = $collection->first();

        $this->assertSame('Another Best One', $dir->getName());
        $this->assertSame('another-best-one', $dir->getSlug());
        $this->assertSame('another-best-one', $dir->getDirName());
        $this->assertInstanceOf(Collection::class, $dir->getFiles());
        $this->assertCount(3, $dir->getFiles());
        $this->assertInstanceOf(Dir::class, $dir);

        $dir = $collection->last();

        $this->assertSame('Another One', $dir->getName());
        $this->assertSame('another-one', $dir->getSlug());
        $this->assertSame('another-one', $dir->getDirName());
        $this->assertInstanceOf(Collection::class, $dir->getFiles());
        $this->assertCount(2, $dir->getFiles());
        $this->assertInstanceOf(Dir::class, $dir);
    }

    public function testFile()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $file          = $markdownPages->file('folder/file-1');

        $this->assertInstanceOf(File::class, $file);
        $this->assertSame('File 1', $file->getName());
        $this->assertSame('file-1', $file->getSlug());
        $this->assertSame('1_folder', $file->getDirName());
        $this->assertSame('folder', $file->getDirNameSlug());

        $this->assertSame('folder/file-1', $file->urlPath());

        $content = <<<'EOT'
            # File 1

            Content goes here

            EOT;
        $this->assertSame($content, $file->load());

        $content = <<<'EOT'
            <h1>File 1</h1>
            <p>Content goes here</p>
            EOT;
        $this->assertSame($content, $file->render());
    }

    public function testFileDoesNotExist()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $file          = $markdownPages->file('folder/file-11111');

        $this->assertNull($file);
    }

    public function testSearch()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $search        = $markdownPages->search('content');

        $this->assertInstanceOf(Results::class, $search);
        $this->assertSame('content', $search->getQuery());

        $results = $search->getResults();
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(4, $results);

        $result = $results->first();
        $this->assertInstanceOf(File::class, $result->getFile());
        $this->assertSame('Info', $result->getFile()->getName());
        $this->assertSame('info', $result->getFile()->getSlug());
        $this->assertSame('x-files/cigarette-smoking-man/info', $result->getFile()->urlPath());
        $this->assertSame(3, $result->getScore());

        $result = $results->next();
        $this->assertInstanceOf(File::class, $result->getFile());
        $this->assertSame('File Name', $result->getFile()->getName());
        $this->assertSame('file-name', $result->getFile()->getSlug());
        $this->assertSame('another-one/file-name', $result->getFile()->urlPath());
        $this->assertSame(2, $result->getScore());

        $result = $results->next();
        $this->assertInstanceOf(File::class, $result->getFile());
        $this->assertSame('A File', $result->getFile()->getName());
        $this->assertSame('a-file', $result->getFile()->getSlug());
        $this->assertSame('folder/a-file', $result->getFile()->urlPath());
        $this->assertSame(1, $result->getScore());

        $result = $results->next();
        $this->assertInstanceOf(File::class, $result->getFile());
        $this->assertSame('File 1', $result->getFile()->getName());
        $this->assertSame('file-1', $result->getFile()->getSlug());
        $this->assertSame('folder/file-1', $result->getFile()->urlPath());
        $this->assertSame(1, $result->getScore());
    }

    public function testSearchWhenNothingFound()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $search        = $markdownPages->search('nothing to found');

        $this->assertInstanceOf(Results::class, $search);
        $this->assertSame('nothing to found', $search->getQuery());

        $results = $search->getResults();
        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(0, $results);
    }
}
