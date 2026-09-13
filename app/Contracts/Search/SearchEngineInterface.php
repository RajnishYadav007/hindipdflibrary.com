<?php

namespace App\Contracts\Search;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SearchEngineInterface
{
    /**
     * Search the published books catalogue.
     *
     * @param  array<string, mixed>  $filters
     */
    public function search(string $query, string $locale, array $filters = [], int $perPage = 12): LengthAwarePaginator;
}
