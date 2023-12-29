# File class

The `File` class represents the single file.

### getName()

The "humanized" file name.

### getSlug()

The file name as a slug. Without optional prefixes used for ordering.

## getPath()

The directory name path with file slug.

### getDepth()

The depth of the file in the tree. Based on a parent folder.

### getFileName()

The file name as it is in the file system.

### getDirName()

The directory name as it is in the file system.

### getDirNamePath()

The directory name path. Without optional prefixes used for ordering.

### load()

Returns the raw content of the file.

##### Parameters

* `$throw` (optional) - Determine whether throw an exception if the file doesn't exist. Default value: `false`.

### parse()

Returns the `Content` class for a file. Learn more about the [Content](content.md) class.

##### Parameters

* `$parseMarkdown` (optional) - Determine whether parse the markdown part of the file. Default value: `true`.

### search()

Returns the "score" for the searched query. Usually not used individually with a file.

##### Parameters

* `$query` - The search query.
* `$metaKeys` (optional) - An array of keys to be included in the search mechanism. Default value: `[]` (empty array).
