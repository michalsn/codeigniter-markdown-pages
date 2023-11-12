<?php

namespace Michalsn\CodeIgniterMarkdownPages\Handlers;

use Michalsn\CodeIgniterMarkdownPages\Interfaces\HandlerInterface;
use Michalsn\CodeIgniterMarkdownPages\Pages\Content;
use Mni\FrontYAML\Parser;

class FrontYamlHandler implements HandlerInterface
{
    protected Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function parse(string $rawContent): Content
    {
        $document = $this->parser->parse($rawContent);
        return new Content($document->getContent(), $document->getYAML());
    }

    public function search(string $query, string $rawContent, array $metaKeys = []): int
    {
        $document = $this->parser->parse($rawContent, false);
        $content  = new Content($document->getContent(), $document->getYAML() ?? []);

        $score = 0;
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
}
