# Search

Classes related to search functionality.

- [Results class](#results-class)
- [Result class](#result-class)

## Results class

The `Results` class handles the results from the `search()` method.

### getQuery()

Get the query used for searching.

### getResults()

Get the Collection with the `Result` classes.

### sortByScore()

Sorts the results by score.

##### Parameters

* `$sort` (optional) - Determine the order for results.
  * `ScoreSortOrder::ASC` - Ascending order
  * `ScoreSortOrder::DESC` - Descending order (default)

## Result class

The `Result` class holds the `File` class and the score for "quality" of search.

### getFile()

Get the `File` class instance for the given file.

### getScore()

Get the score that determines the quality of the search. The higher, the better.

