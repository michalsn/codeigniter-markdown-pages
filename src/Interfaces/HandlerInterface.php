<?php

namespace Michalsn\CodeIgniterMarkdownPages\Interfaces;

interface HandlerInterface
{
    public function parse(string $string): string;
}