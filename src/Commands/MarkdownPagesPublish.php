<?php

namespace Michalsn\CodeIgniterMarkdownPages\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Publisher\Publisher;
use Throwable;

class MarkdownPagesPublish extends BaseCommand
{
    protected $group       = 'MarkdownPages';
    protected $name        = 'markdownpages:publish';
    protected $description = 'Publish Markdown Pages config file into the current application.';

    /**
     * @return void
     */
    public function run(array $params)
    {
        $source = service('autoloader')->getNamespace('Michalsn\\CodeIgniterMarkdownPages')[0];

        $publisher = new Publisher($source, APPPATH);

        try {
            $publisher->addPaths([
                'Config/MarkdownPages.php',
            ])->merge(false);
        } catch (Throwable $e) {
            $this->showError($e);

            return;
        }

        foreach ($publisher->getPublished() as $file) {
            $contents = file_get_contents($file);
            $contents = str_replace('namespace Michalsn\\CodeIgniterMarkdownPages\\Config', 'namespace Config', $contents);
            $contents = str_replace('use CodeIgniter\\Config\\BaseConfig', 'use Michalsn\\CodeIgniterMarkdownPages\\Config\\MarkdownPages as BaseMarkdownPages', $contents);
            $contents = str_replace('class MarkdownPages extends BaseConfig', 'class MarkdownPages extends BaseMarkdownPages', $contents);
            file_put_contents($file, $contents);
        }

        CLI::write(CLI::color('  Published! ', 'green') . 'You can customize the configuration by editing the "app/Config/MarkdownPages.php" file.');
    }
}
