<?php

namespace Tests\Support\Config;

use Michalsn\CodeIgniterMarkdownPages\Config\MarkdownPages as BaseMarkdownPages;

class MarkdownPages extends BaseMarkdownPages
{
    /**
     * YAML Parser.
     *
     * By default, uses Symfony's YAML parser.
     */
    public ?string $yamlParser = null;

    /**
     * Markdown Parser.
     *
     * By default, uses League CommonMark parser.
     */
    public ?string $markdownParser = null;

    /**
     * Files with this extension will be taken
     * into consideration when scanning folders.
     */
    public string $fileExtension = 'md';
}
