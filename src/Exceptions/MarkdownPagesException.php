<?php

namespace Michalsn\CodeIgniterMarkdownPages\Exceptions;

use RuntimeException;

final class MarkdownPagesException extends RuntimeException
{
    public static function forIncorrectFolderPath(): static
    {
        return new self(lang('MarkdownPages.incorrectFolderPath'));
    }

    public static function forIncorrectHandler(): static
    {
        return new self(lang('MarkdownPages.incorrectHandler'));
    }

    public static function forFileDoesNotExist(string $name): static
    {
        return new self(lang('MarkdownPages.fileDoesNotExist', [$name]));
    }
}
