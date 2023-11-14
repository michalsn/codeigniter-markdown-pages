# Content class

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

