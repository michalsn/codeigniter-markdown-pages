# Extending

You can define your own Markdown or YAML parser if you want.

By default, this package uses `League CommonMark parser` and `Symfony's YAML parser`.

## Your own parser

In this example I will show you how you can define `GitHub-Flavored Markdown` instead of the default `CommonMark parser`.

Let's create our new parser.

```php
<?php

namespace App\Parsers;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\ConverterInterface;
use League\Config\Exception\ConfigurationExceptionInterface;
use Mni\FrontYAML\Markdown\MarkdownParser;

class GithubFlavoredMarkdown implements MarkdownParser
{
    private ConverterInterface $parser;

    public function __construct()
    {
        // Define your configuration, if needed
        $config = [];

        // Configure the Environment with all the CommonMark and GFM parsers/renderers
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        $this->parser = new MarkdownConverter($environment);
    }

    /**
     * @throws ConfigurationExceptionInterface
     * @throws CommonMarkException
     */
    public function parse(string $markdown): string
    {
        return $this->parser->convert($markdown);
    }
}
```

Now, the only thing we need to do is to use this new parser in our config file.

If you haven't done this yet, publish the config file with command:

```console
php spark markdownpages:publish
```

Now let's edit a config file we just published.

```php
// app/Config/MarkdownPages.php
<?php

namespace Config;

use App\Parsers\GithubFlavoredMarkdown;
use Michalsn\CodeIgniterMarkdownPages\Config\MarkdownPages as BaseMarkdownPages;

class MarkdownPages extends BaseMarkdownPages
{
    // ...

    /**
     * Markdown Parser.
     *
     * By default, uses League CommonMark parser.
     */
    public ?string $markdownParser = GithubFlavoredMarkdown::class;

    // ...
}
```

And that's it. You can now take full advantage of Github-Flavored Markdown parser. Based on this example, you can define any extension you want or even use a totally different Markdown parser provider.
