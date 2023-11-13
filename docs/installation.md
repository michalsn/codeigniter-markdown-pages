# Installation

- [Composer Installation](#composer-installation)
- [Manual Installation](#manual-installation)

## Composer Installation

The only thing you have to do is to run this command, and you're ready to go.

```console
composer michalsn/codeigniter-markdown-pages
```

## Manual Installation

In the example below we will assume, that files from this project will be located in `app/ThirdParty/markdown-pages` directory.

Download this project and then enable it by editing the `app/Config/Autoload.php` file and adding the `Michalsn\CodeIgniterMarkdownPages` namespace to the `$psr4` array, like in the below example:

```php
<?php

// ...

public $psr4 = [
    APP_NAMESPACE => APPPATH, // For custom app namespace
    'Config'      => APPPATH . 'Config',
    'Michalsn\CodeIgniterMarkdownPages' => APPPATH . 'ThirdParty/markdown-pages/src',
];

// ...
```

The last thing - you still have to install additional libraries via composer:

```console
composer mnapoli/front-yaml myth/collection
```
