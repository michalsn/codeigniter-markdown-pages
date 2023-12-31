<?php

namespace Tests;

use CodeIgniter\Test\ReflectionHelper;
use Michalsn\CodeIgniterMarkdownPages\Exceptions\MarkdownPagesException;
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
final class MarkdownPagesTest extends TestCase
{
    use ReflectionHelper;

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

    public function testDir()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $dir           = $markdownPages->dir('folder');

        $this->assertInstanceOf(Dir::class, $dir);

        $this->assertSame('Folder', $dir->getName());
        $this->assertSame('folder', $dir->getSlug());
        $this->assertSame('1_folder', $dir->getDirName());
        $this->assertTrue($dir->hasParent());
        $this->assertFalse($dir->hasChildren());
        $this->assertSame([], $dir->getChildren());
        $this->assertInstanceOf(Collection::class, $dir->getFiles());
        $this->assertTrue($dir->hasFiles());
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

        $this->assertCount(9, $collection);

        $this->assertSame('', $collection->first()->getPath());
        $this->assertSame('folder', $collection->next()->getPath());
        $this->assertSame('x-files', $collection->next()->getPath());
        $this->assertSame('x-files/danna-scully', $collection->next()->getPath());
        $this->assertSame('x-files/cigarette-smoking-man', $collection->next()->getPath());
        $this->assertSame('x-files/fox-mulder', $collection->next()->getPath());
        $this->assertSame('another-best-one', $collection->next()->getPath());
        $this->assertSame('another-best-one/empty-folder', $collection->next()->getPath());
        $this->assertSame('another-one', $collection->next()->getPath());
    }

    public function testDirsWithNullAndDepth()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $collection    = $markdownPages->depth(1)->dirs();

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertCount(5, $collection);

        $this->assertSame('', $collection->first()->getSlug());
        $this->assertSame(0, $collection->first()->getDepth());
        $next = $collection->next();
        $this->assertSame('folder', $next->getSlug());
        $this->assertSame(1, $next->getDepth());
        $next = $collection->next();
        $this->assertSame('x-files', $next->getSlug());
        $this->assertSame(1, $next->getDepth());
        $next = $collection->next();
        $this->assertSame('another-best-one', $next->getSlug());
        $this->assertSame(1, $next->getDepth());
        $next = $collection->next();
        $this->assertSame('another-one', $next->getSlug());
        $this->assertSame(1, $next->getDepth());
    }

    public function testDirsWithNullAndDepthArray()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $collection    = $markdownPages->depth([1, 2])->dirs();

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertCount(8, $collection);
    }

    public function testDirsWithNullAndDepthAndParent()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $collection    = $markdownPages->depth(1)->parent('')->dirs();

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertCount(4, $collection);

        $first = $collection->first();
        $this->assertSame('folder', $first->getSlug());

        $next = $collection->next();
        $this->assertSame('x-files', $next->getSlug());
        $this->assertSame(1, $next->getDepth());
        $this->assertTrue($next->getFiles()->isEmpty());

        $next = $collection->next();
        $this->assertSame('another-best-one', $next->getSlug());
        $this->assertSame(1, $next->getDepth());

        $next = $collection->next();
        $this->assertSame('another-one', $next->getSlug());
        $this->assertSame(1, $next->getDepth());
    }

    public function testDirsWithParent()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $collection    = $markdownPages->parent('x-files')->dirs();

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertCount(3, $collection);
    }

    public function testDirsWithParentStartWith()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $collection    = $markdownPages->parent('x*')->dirs();

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertCount(3, $collection);
    }

    public function testDirsWithParentArrayNull()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $collection    = $markdownPages->parent([null])->dirs();

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertCount(1, $collection);
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

        $this->assertCount(3, $collection);

        $dir = $collection->first();

        $this->assertSame('Another Best One', $dir->getName());
        $this->assertSame('another-best-one', $dir->getSlug());
        $this->assertSame('another-best-one', $dir->getPath());
        $this->assertSame('another-best-one', $dir->getDirName());
        $this->assertInstanceOf(Collection::class, $dir->getFiles());
        $this->assertCount(3, $dir->getFiles());
        $this->assertInstanceOf(Dir::class, $dir);

        $dir = $collection->next();

        $this->assertSame('Empty Folder', $dir->getName());
        $this->assertSame('empty-folder', $dir->getSlug());
        $this->assertSame('another-best-one/empty-folder', $dir->getPath());
        $this->assertSame('another-best-one/empty-folder', $dir->getDirName());
        $this->assertInstanceOf(Collection::class, $dir->getFiles());
        $this->assertTrue($dir->getFiles()->isEmpty());
        $this->assertInstanceOf(Dir::class, $dir);

        $dir = $collection->last();

        $this->assertSame('Another One', $dir->getName());
        $this->assertSame('another-one', $dir->getSlug());
        $this->assertSame('another-one', $dir->getPath());
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
        $this->assertSame('folder', $file->getDirNamePath());
        $this->assertSame(1, $file->getDepth());

        $this->assertSame('folder/file-1', $file->getPath());

        $rawContent = <<<'EOT'
            # File 1

            Content goes here

            EOT;
        $this->assertSame($rawContent, $file->load());

        $content = $file->parse();
        $this->assertInstanceOf(Content::class, $content);

        $parsedContent = <<<'EOT'
            <h1>File 1</h1>
            <p>Content goes here</p>

            EOT;
        $this->assertSame($parsedContent, $content->getContent());
    }

    public function testFileLoadException()
    {
        $this->expectException(MarkdownPagesException::class);

        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $file          = $markdownPages->file('folder/file-1');

        $this->setPrivateProperty($file, 'fileName', 'error');
        $file->load(true);
    }

    public function testFileDoesNotExist()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $file          = $markdownPages->file('folder/file-11111');

        $this->assertNull($file);
    }

    public function testFileWithHtmlExtension()
    {
        $this->config->fileExtension = 'html';
        $markdownPages               = new MarkdownPages($this->folderPath, $this->config);
        $file                        = $markdownPages->file('folder/surprise');

        $this->assertInstanceOf(File::class, $file);
        $this->assertSame('Surprise', $file->getName());
        $this->assertSame('surprise', $file->getSlug());
        $this->assertSame('1_folder', $file->getDirName());
        $this->assertSame('folder', $file->getDirNamePath());

        $this->assertSame('folder/surprise', $file->getPath());

        $rawContent = <<<'EOT'
            <h1>Surprise</h1>

            <p>Html extension</p>

            EOT;
        $this->assertSame($rawContent, $file->load());

        $content = $file->parse(false);
        $this->assertInstanceOf(Content::class, $content);
        $this->assertSame($rawContent, $content->getContent());
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
        $this->assertSame('x-files/cigarette-smoking-man/info', $result->getFile()->getPath());
        $this->assertSame(3, $result->getScore());

        $result = $results->next();
        $this->assertInstanceOf(File::class, $result->getFile());
        $this->assertSame('File Name', $result->getFile()->getName());
        $this->assertSame('file-name', $result->getFile()->getSlug());
        $this->assertSame('another-one/file-name', $result->getFile()->getPath());
        $this->assertSame(2, $result->getScore());

        $result = $results->next();
        $this->assertInstanceOf(File::class, $result->getFile());
        $this->assertSame('A File', $result->getFile()->getName());
        $this->assertSame('a-file', $result->getFile()->getSlug());
        $this->assertSame('folder/a-file', $result->getFile()->getPath());
        $this->assertSame(1, $result->getScore());

        $result = $results->next();
        $this->assertInstanceOf(File::class, $result->getFile());
        $this->assertSame('File 1', $result->getFile()->getName());
        $this->assertSame('file-1', $result->getFile()->getSlug());
        $this->assertSame('folder/file-1', $result->getFile()->getPath());
        $this->assertSame(1, $result->getScore());
    }

    public function testSearchWithKeys()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $results       = $markdownPages->search('title', null, ['title']);
        $this->assertInstanceOf(Results::class, $results);

        $this->assertCount(1, $results->getResults());

        $result = $results->getResults()->first();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(2, $result->getScore());

        $file = $result->getFile();
        $this->assertInstanceOf(File::class, $file);

        $this->assertSame('A Last One', $file->getName());
        $this->assertSame('another-one/a-last-one', $file->getPath());

        $content = $file->parse();
        $this->assertInstanceOf(Content::class, $content);

        $parsedContent = <<<'EOT'
            <h2>A last one</h2>
            <p>the last page title is above</p>

            EOT;
        $this->assertSame($parsedContent, $content->getContent());

        $meta = [
            'title' => 'Sample title',
        ];
        $this->assertSame($meta, $content->getMeta());
        $this->assertSame($meta['title'], $content->getMeta('title'));
        $this->assertNull($content->getMeta('invalid'));
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
