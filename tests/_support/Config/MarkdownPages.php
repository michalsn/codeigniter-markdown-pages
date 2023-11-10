<?php

namespace Tests\Support\Config;

use Michalsn\CodeIgniterMarkdownPages\Config\MarkdownPages as BaseMarkdownPages;
use Michalsn\CodeIgniterMarkdownPages\Handlers\DummyHandler;
use Michalsn\CodeIgniterMarkdownPages\Handlers\ParsedownHandler;

class MarkdownPages extends BaseMarkdownPages
{
    public string $defaultHandler = 'parsedown';
    public array $handlers        = [
        'dummy'     => DummyHandler::class,
        'parsedown' => ParsedownHandler::class,
    ];
}
