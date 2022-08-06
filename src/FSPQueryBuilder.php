<?php

namespace TamayozQuery;

use TamayozQuery\Classes\QuerySorter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

trait FSPQueryBuilder
{
    /**
     * filter a query based on request options
     *
     */
    public function scopeWithFilters($query)
    {
        //check if there is any filters
        if(property_exists($this, 'filterable'))
            $query->filterable(request()->filter);

        return $query;
    }

    /**
     * sort a query based on request options
     */
    public function scopeWithSorts($query, $secondQuery = null)
    {
        if($secondQuery)
            $query = $secondQuery;

        //check if there is any sortable fields
        if(property_exists($this, 'sortable'))
            $query->sort(app(QuerySorter::class), $query);
        return $query;
    }

    /**
     * @param $query
     * Handling Pagination
     * @return LengthAwarePaginator | Collection
     */
    public function scopeWithPagination($query, $secondQuery = null)
    {
        if($secondQuery)
            $query = $secondQuery;

        //dealing with pagination if exist
        if(request()->perPage || request()->page)
            return $query->paginate(request()->perPage ?? null)->appends(request()->all());

        return $query->get();
    }
}
