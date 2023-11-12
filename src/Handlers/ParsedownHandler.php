<?php

namespace Michalsn\CodeIgniterMarkdownPages\Handlers;

use Michalsn\CodeIgniterMarkdownPages\Interfaces\HandlerInterface;
use Michalsn\CodeIgniterMarkdownPages\Pages\Content;
use Parsedown;

class ParsedownHandler implements HandlerInterface
{
    protected Parsedown $parser;

    public function __construct()
    {
        $this->parser = new Parsedown();
    }

    public function parse(string $rawContent): Content
    {
        return new Content($this->parser->text($rawContent));
    }

    public function search(string $query, string $rawContent, array $metaKeys = []): int
    {
        return mb_substr_count($rawContent, $query);
    }
}
