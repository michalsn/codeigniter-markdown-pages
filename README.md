# CodeIgniter Markdown Pages

Markdown based pages for the CodeIgniter 4 framework.

[![PHPUnit](https://github.com/michalsn/codeigniter-markdown-pages/actions/workflows/phpunit.yml/badge.svg)](https://github.com/michalsn/codeigniter-markdown-pages/actions/workflows/phpunit.yml)
[![PHPStan](https://github.com/michalsn/codeigniter-markdown-pages/actions/workflows/phpstan.yml/badge.svg)](https://github.com/michalsn/codeigniter-markdown-pages/actions/workflows/phpstan.yml)
[![Deptrac](https://github.com/michalsn/codeigniter-markdown-pages/actions/workflows/deptrac.yml/badge.svg)](https://github.com/michalsn/codeigniter-markdown-pages/actions/workflows/deptrac.yml)
[![Coverage Status](https://coveralls.io/repos/github/michalsn/codeigniter-markdown-pages/badge.svg?branch=develop)](https://coveralls.io/github/michalsn/codeigniter-markdown-pages?branch=develop)

![PHP](https://img.shields.io/badge/PHP-%5E8.1-blue)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-%5E4.3-blue)

## Installation

    composer require michalsn/codeigniter-markdown-pages

## Basic usage

To easily demonstrate how the things are working we will assume for a moment that this is our folder structure.

```bash
├── app
├── content
│   ├── 1_quick-start
│   │   ├── 1_installation.md
│   │   ├── 2_database-migration.md
│   │   ├── what-next.md
│   │   └── subfolder
│   │       └── one-file.md
│   └── first-steps
│       ├── available-methods.md
│       └── how-to-use-this.md
├── public
├── tests
├── vendor
└── writable
```

Now we have to initialize Markdown Pages with our folder:

```php
$markdownPages = services('markdownpages', ROOTPATH . 'content');

$dir = $markdownPages->dirs()->first();

echo $dir->getName()
// prints: Quick Start

echo $dir->getSlug()
// prints: quick-start

foreach($dir->getFiles()->items() as $file) {
    echo $file->getName();
    // prints: Installation

    echo $file->getSlug();
    // prints: installation

    echo $file->urlPath();
    // prints: quick-start/installation

    echo $content->parse()->getContent();
    // prints: parsed markdown from file

    echo $content->parse()->getMeta();
    // prints: parsed YAML as key -> value
}
```

We use [Collection](https://github.com/lonnieezell/myth-collection) class pretty much everywhere so please get familiar with it to use this package comfortably.

## Docs

https://michalsn.github.io/codeigniter-markdown-pages/
