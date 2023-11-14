# Dir class

The `Dir` class represents the single folder.

### getName()

The "humanized" folder name.

### getSlug()

The directory name slug. Without optional prefixes used for ordering.

### getPath()

The parent directory path and current folder slug.

### getDepth()

The depth of the directory in the tree.

### getParent()

The parent directory path.

### hasParent()

Returns `true` or `false` depending on whether the parent exists or not.

### getChildren()

Returns an array of children folders in slug format.

### hasChildren()

Returns `true` or `false` depending on whether children exists or not.

### getFiles()

Returns the Collection of `File` classes or empty Collection.

Learn more about the [File](classes/file.md) class.

### hasFiles()

Returns `true` or `false` depending on whether files exists or not.

### getDirName()

The directory name as it is in the file system.
