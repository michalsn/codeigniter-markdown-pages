<?php

namespace Michalsn\CodeIgniterMarkdownPages\Pages;

class Content
{
    public function __construct(protected string $content, protected array $meta = [])
    {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getMeta(?string $key = null): string|array|int|null
    {
        if ($key === null) {
            return $this->meta;
        }

        return $this->meta[$key] ?? null;
    }


    public function hasMetaKey(string $key): bool
    {
        return array_key_exists($key, $this->meta);
    }
}
