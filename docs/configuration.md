# Configuration

To make changes to the config file, we have to have our copy in the `app/Config/MarkdownPages.php`. Luckily, this package comes with handy command that will make this easy.

When we run:

    php spark markdownpages:publish

We will get our copy ready for modifications.

---

Available options:

- [$yamlParser](#yamlParser)
- [$markdownParser](#markdownParser)
- [$fileExtension](#fileExtension)

### $yamlParser

The class that will be used to parse YAML part of the file.

You're not required to include YAML to your file, though.

Default value: `null`.

With the default value, [symfony/yaml](https://github.com/symfony/yaml) package is used.

### $markdownParser

The class that will be used to parse Markdown part of the file.

Default value: `null`.

With the default value, [league/commonmark](https://github.com/thephpleague/commonmark) package is used.

### $fileExtension

The files with only this extension will be considered when mapping the directories.

Default value: `md`.

