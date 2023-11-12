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
final class MarkdownPagesWithFrontYamlHandlerTest extends TestCase
{
    private string $folderPath;
    private MarkdownPagesConfig $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->folderPath             = SUPPORTPATH . 'Pages';
        $this->config                 = config(MarkdownPagesConfig::class);
        $this->config->defaultHandler = 'frontyaml';
    }

    public function testFile()
    {
        $markdownPages = new MarkdownPages($this->folderPath, $this->config);
        $file          = $markdownPages->file('another-one/a-last-one');

        $this->assertInstanceOf(File::class, $file);
        $this->assertSame('A Last One', $file->getName());
        $this->assertSame('a-last-one', $file->getSlug());
        $this->assertSame('another-one', $file->getDirName());
        $this->assertSame('another-one', $file->getDirNameSlug());

        $this->assertSame('another-one/a-last-one', $file->urlPath());

        $rawContent = <<<'EOT'
            ---
            title: Sample title
            ---

            ## A last one

            the last page title is above

            EOT;
        $this->assertSame($rawContent, $file->load());

        $content = $file->parse();
        $this->assertInstanceOf(Content::class, $content);

        $parsedContent = <<<'EOT'
            <h2>A last one</h2>
            <p>the last page title is above</p>

            EOT;
        $this->assertSame($parsedContent, $content->getContent());
    }

    public function testSearch()
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
        $this->assertSame('another-one/a-last-one', $file->urlPath());

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
}
