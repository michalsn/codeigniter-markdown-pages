<?php

namespace Michalsn\CodeIgniterMarkdownPages\Handlers;

use Michalsn\CodeIgniterMarkdownPages\Interfaces\HandlerInterface;
use Parsedown;

class ParsedownHandler implements HandlerInterface
{
    protected Parsedown $parser;

    public function __construct()
    {
        $this->parser = new Parsedown();
    }

    public function parse(string $string): string
    {
        return $this->parser->text($string);
    }
}