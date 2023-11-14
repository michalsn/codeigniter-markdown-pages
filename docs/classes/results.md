# Results class

The `Results` class handles the results from the `search()` method.

### getQuery()

Get the query used for searching.

### getResults()

Get the Collection with the `Result` classes. Learn more about the [Result](classes/result.md) class.

### sortByScore()

Sorts the results by score.

##### Parameters

* `$sort` (optional) - Determine the order for results.
    * `ScoreSortOrder::ASC` - Ascending order
    * `ScoreSortOrder::DESC` - Descending order (default)
