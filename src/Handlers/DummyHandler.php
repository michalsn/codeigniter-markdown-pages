<?php

namespace Michalsn\CodeIgniterMarkdownPages\Handlers;

use Michalsn\CodeIgniterMarkdownPages\Interfaces\HandlerInterface;
use Michalsn\CodeIgniterMarkdownPages\Pages\Content;

class DummyHandler implements HandlerInterface
{
    public function parse(string $rawContent): Content
    {
        return new Content($rawContent);
    }

    public function search(string $query, string $rawContent, array $metaKeys = []): int
    {
        return mb_substr_count($rawContent, $query);
    }
}
