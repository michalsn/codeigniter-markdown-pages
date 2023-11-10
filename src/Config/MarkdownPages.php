<?php

namespace Michalsn\CodeIgniterMarkdownPages\Config;

use CodeIgniter\Config\BaseConfig;
use Michalsn\CodeIgniterMarkdownPages\Handlers\DummyHandler;
use Michalsn\CodeIgniterMarkdownPages\Handlers\ParsedownHandler;

class MarkdownPages extends BaseConfig
{
    /**
     * The default parser handler.
     */
    public string $defaultHandler = 'parsedown';

    /**
     * Available parser handlers.
     */
    public array $handlers = [
        'dummy'     => DummyHandler::class,
        'parsedown' => ParsedownHandler::class,
    ];

    /**
     * Files with this extension will be taken
     * into consideration when scanning folders.
     */
    public string $fileExtension = 'md';
}