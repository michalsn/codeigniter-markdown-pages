<?php

namespace Michalsn\CodeIgniterMarkdownPages\Handlers;

use Michalsn\CodeIgniterMarkdownPages\Interfaces\HandlerInterface;

class DummyHandler implements HandlerInterface
{
    public function parse(string $string): string
    {
        return $string;
    }
}
