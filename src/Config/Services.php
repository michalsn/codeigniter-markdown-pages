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
    public static function markdownpages(string $baseFolder, ?MarkdownPagesConfig $config = null, bool $getShared = true): MarkdownPages
    {
        if ($getShared) {
            return static::getSharedInstance('markdownpages', $baseFolder, $config);
        }

        $config ??= config('MarkdownPages');

        /** @var MarkdownPagesConfig $config */
        return new MarkdownPages($baseFolder, $config);
    }
}
