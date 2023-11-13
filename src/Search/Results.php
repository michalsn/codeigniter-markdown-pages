<?php

namespace Michalsn\CodeIgniterMarkdownPages\Search;

use Michalsn\CodeIgniterMarkdownPages\Enums\ScoreSortOrder;
use Myth\Collection\Collection;

class Results
{
    protected Collection $results;

    public function __construct(protected string $query)
    {
        $this->results = new Collection([]);
    }

    /**
     * Get search query.
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Get results collection.
     */
    public function getResults(): Collection
    {
        return $this->results;
    }

    /**
     * Sort results.
     */
    public function sortByScore(ScoreSortOrder $sort = ScoreSortOrder::DESC): static
    {
        if ($this->results->isEmpty()) {
            return $this;
        }

        $this->results = $this->results->{$sort->value}(static fn ($result) => $result->getScore());

        return $this;
    }
}
