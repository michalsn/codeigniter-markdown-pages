<?php

namespace Michalsn\CodeIgniterMarkdownPages\Config;

use CodeIgniter\Config\BaseService;
use Michalsn\CodeIgniterMarkdownPages\Config\MarkdownPages as MarkdownPagesConfig;
use Michalsn\CodeIgniterMarkdownPages\MarkdownPages;

class Services extends BaseService
{
    /**
     * Return the markdown pages class.
     */
    public static function markdownpages(string $folderPath, ?MarkdownPagesConfig $config = null, bool $getShared = true): MarkdownPages
    {
        if ($getShared) {
            return static::getSharedInstance('markdownpages', $folderPath, $config);
        }

        /** @var MarkdownPagesConfig $config */
        $config ??= config('MarkdownPages');

        return new MarkdownPages($folderPath, $config);
    }
}
