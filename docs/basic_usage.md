# Basic usage

- [New Instance](#new-instance)
- [Main Methods](#main-methods)
    - [dirs()](#dirs)
    - [dir()](#dir)
    - [search()](#search)
    - [file()](#file)
- [Helper Methods](#helper-methods)
    - [depth()](#depth)
    - [parent()](#parent)

## New Instance

To create new instance we simply have to call our service along with the folder we want to work with:

```php
$markdownPages = service('markdownpages', ROOTPATH . 'pages');
```

In many places we use [Collection](https://github.com/lonnieezell/myth-collection) to work with the data. If you're not familiar with the idea of collections, please get familiar with it to use this package comfortably.

## Main Methods

These methods are used to retrieve information about the elements inside the folder with which we created an instance.

This directory structure will be a good reference for explaining of how things work.

```bash
└── pages
    ├── 1_quick-start
    │   ├── 1_installation.md
    │   ├── 2_database-migration.md
    │   ├── what-next.md
    │   └── subfolder
    │       └── one-file.md
    └── first-steps
        ├── how-to-use-this.md
        └── second-file.md
```

As you may notice some folders and files are prefixed with the number. This number is used strictly for ordering purposes and is not used when determining the `slug` or `path` for the directories and files.

!!! note

    Folders and files names are supposed to include only alphanumeric characters in ASCII and dashes.

!!! warning

    Underscores are reserved to postfix the sorting number, which (when avaliable) should be always at the beginning.

### dirs()

This method allow us to retrieve the folders we loaded. There are a couple of ways to specify the directory we're looking for.

##### Parameters

* `$path` (optional) - We can use:
    * The default `null` value. To get all the folders.
    * A simple **string**: `'quick-start/subfolder'`, to get the certain folder.
    * A **string with wildcard**: `'quick-start*'`, to get all the folders that include this path.
    * An **array** of folders: `['quick-start/subfolder', 'first-steps']`, to get may folders.

##### Examples

This method will always return a Collection of folders (`Dir` classes). Learn more about the [Dir](classes/dir.md) class.

```php
// returns Collection class with Dir classes
$markdownPages->dirs();
```

This will return a Collection of all folders that belongs to given folder path wildcard. In this case: `quick-start` and `subfolder`.

```php
// returns Collection class with Dir classes
$markdownPages->dirs('quick-start*');
```

We can also use helper methods to make more complicated operations.

This will return a Collection of all folders that belongs to given folder path wildcard and are at the given depth. The result will include: `subfolder`.

```php
// returns Collection class with Dir classes
$markdownPages->depth([2])->dirs('quick-start*');
```

We can also retrieve folders based on the parent folder. This will return a Collection with the `subfolder`.

```php
// returns Collection class with Dir classes
$markdownPages->parent('quick-start')->dirs();
```

### dir()

This method works the same way as `dirs()`, but instead of returning a Collection of `Dir` classes it will return a single `Dir` class or `null` if nothing can be found.

It will also not accept the `null` value as a parameter.

##### Parameters

* `$path` - We can use:
    * A simple **string**: `'quick-start/subfolder'`, to get the certain folder.
    * A **string with wildcard**: `'quick-start*'`, to get all the folders that include this path.
    * An **array** of folders: `['quick-start/subfolder', 'first-steps']`, to get may folders.

##### Examples

```php
// returns Dir class or null
$dir = $markdownPages->dir('quick-start/subfolder');
// returns "Subfolder"
$dir->getName();
// returns 2
$dir->getDepth();
// returns "quick-start"
$dir->getParent();
// returns Collection of File classes
$dir->getFiles();
```

When using a wildcard or any other parameter that can result in matching multiple folders, the first one is returned.

Learn more about the [Dir](classes/dir.md) class.

### search()

This method will search for a given sentence and return a `Results` class. The search will occur in a couple of places:

* The file name.
* The content of the markdown file.
* And optionally in the YAML content. To make this work, we have to specify which YAML `keys` should be taken into consideration.

##### Parameters

* `$query` - The search query.
* `$path` (optional) - We can use:
    * The default `null` value. To get all the folders.
    * A simple **string**: `'quick-start/subfolder'`, to get the certain folder.
    * A **string with wildcard**: `'quick-start*'`, to get all the folders that include this path.
    * An **array** of folders: `['quick-start/subfolder', 'first-steps']`, to get may folders.
* `$metaKeys` (optional) - An array of meta keys to be considered when searching for a query. You have to use YAML part in your files to make it work. Default value: `[]` (empty array)

##### Examples

```php
// search for "file" keyword
$markdownPages->search('file');
// or with metaKeys if we're using YAML with those keys
$results = $markdownPages->search('file', ['title', 'description']);
// returns the search query: "file"
$results->getQuery();
// returns the Collection with Result class
$results->getResults();
```

Learn more about the [Result](classes/result.md) class.

The search is case-insensitive. It's very basic and count the number of occurrences for the searched word. The number of occurrences is used to order the results.

The above search should return 2 results, for: `one-file.md` and `second-file.md`.

We can also use `search()` with other helper methods:

```php
$markdownPages->depth([2])->search('file');
```

The above will return only 1 result: `one-file.md`. The next example will also return only one result, but it will be `second-file.md`.

```php
$markdownPages->parent('')->search('file');
```

!!! note

    Both methods (`depth()` and `parent()`) refer as in previous cases to the **folder** where the file is located, not to the file itself.

Learn more about the [Results](classes/results.md) class.

### file()

This method is used to load the file to the `File` class.

##### Parameters

* `$path` - The full **getPath** which includes parent folder path and file slug.

##### Examples

```php
$file = $markdownPages->file('first-steps/second-file');
// returns "Second File"
$file->getName();
// returns parsed markdown
$file->parse()->getContent();
```

Learn more about the [File](classes/file.md) class.

## Helper Methods

These helper methods allow you to change the behavior of the methods: `dirs()`, `dir()` and `search()`.

### depth()

It will act differently depending on what value will be passed.

##### Parameters

* `$depth` - It can be:
    * **int** - will include all folders that depth is smaller than provided value (including that number).
    * **array** - will include folders only from given depth.

##### Examples

The example below will select `Dir` classes: `quick-start` and `first-steps`.

```php
$markdownPages->depth(1);
// this is an equivalent of above
$markdownPages->depth([0, 1]);
```

### parent()

It will act differently depending on what value will be passed.

##### Parameters

* `$parent` - It can be:
    * **string** - will include folders only with that parent.
    * **string with wildcard** - will include all the folders which parent match the pattern.
    * **array** - will include all folders which has parent listed in the array

##### Examples

This will select the `Dir` class with folder `subfolder`.

```php
// string
$markdown->parent('quick-start');
```

This will select `Dir` classes with folder `quick-start` and `subfolder`.

```php
// string with wildcard
$markdown->parent('quick-start*');
```

This will select the `Dir` class with folder `subfolder`.

```php
// array
$markdown->parent(['quick-start']);
```
