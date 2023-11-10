<?php

namespace Michalsn\CodeIgniterMarkdownPages\Search;

use Michalsn\CodeIgniterMarkdownPages\Pages\File;

class Result
{
    public function __construct(protected File $file, protected int $score)
    {
    }

    /**
     * Get file class.
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * Get result score.
     */
    public function getScore(): int
    {
        return $this->score;
    }
}
