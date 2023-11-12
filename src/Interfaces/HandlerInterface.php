<?php

namespace Michalsn\CodeIgniterMarkdownPages\Interfaces;

use Michalsn\CodeIgniterMarkdownPages\Pages\Content;

interface HandlerInterface
{
    public function parse(string $rawContent): Content;

    public function search(string $query, string $rawContent, array $metaKeys = []): int;
}
