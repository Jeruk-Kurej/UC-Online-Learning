<?php

namespace App\Traits;

/**
 * Newest rows first, with id as the tie-breaker: imported rows share one created_at, and without a unique last sort key
 * MySQL may order them differently for page 1 and page 2, so a row shows twice and another never.
 */
trait OrdersNewestFirst
{
    public function scopeNewestFirst($query)
    {
        return $query->orderByDesc('created_at')->orderByDesc('id');
    }
}
