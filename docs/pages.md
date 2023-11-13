# Pages

All pages related classes.

- [Dir class](#dir-class)
- [File class](#file-class)
- [Content class](#content-class)

## Dir class

The `Dir` class represents the single folder.

### getName()

The "humanized" folder name.

### getSlug()

The directory name as a slug. Without optional prefixes used for ordering.

### getDepth()

The depth of the directory in the tree.

### getParent()

The parent directory slug.

### hasParent()

Returns `true` or `false` depending on whether the parent exists or not.

### getChildren()

Returns an array of children folders in slug format.

### hasChildren()

Returns `true` or `false` depending on whether children exists or not.

### getFiles()

Returns the Collection of `File` classes or empty Collection.

### hasFiles()

Returns `true` or `false` depending on whether files exists or not.

### getDirName()

The directory name as it is in the file system.

## File class

The `File` class represents the single file.

### getName()

The "humanized" file name.

### getSlug()

The file name as a slug. Without optional prefixes used for ordering.

### getDepth()

The depth of the file in the tree. Based on a parent folder.

### getFileName()

The file name as it is in the file system.

### getDirName()

The directory name as it is in the file system.

### getDirNameSlug()

The directory name as a slug. Without optional prefixes used for ordering.

### urlPath()

The URL path to use for navigation. Include full slug for directory and file.

### load()

Returns the raw content of the file.

##### Parameters

* `$throw` (optional) - Determine whether throw an exception if the file doesn't exist. Default value: `false`.

### parse()

Returns the `Content` class for a file.

##### Parameters

* `$parseMarkdown` (optional) - Determine whether parse the markdown part of the file. Default value: `true`.

### search()

Returns the "score" for the searched query. Usually not used individually with a file.

##### Parameters

* `$query` - The search query.
* `$metaKeys` (optional) - An array of keys to be included in the search mechanism. Default value: `[]` (empty array).

## Content class

The `Content` class represents the single file content.

### getContent()

Returns the parsed markdown part of the file.

### getMeta()

Returns the parsed YAML part of the file.

##### Parameters

* `$key` (optional) - Determine what meta key should be returned. Returns all the keys when set to `null`. Default value: `null`.

### hasMetaKey()

Returns `true` or `false` depending on whether the given key exists.

##### Parameters

* `$key` - The key for the meta array.



