<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait CanBeBillable
{
    /**
     * @param Builder<\App\Models\Session> $query
     *
     * @return Builder<\App\Models\Session>
     */
    public function scopeWhereBillable(Builder $query): Builder
    {
        return $query->where('is_billable', 1);
    }

    /**
     * @param Builder<\App\Models\Session> $query
     *
     * @return Builder<\App\Models\Session>
     */
    public function scopeWhereNotBillable(Builder $query): Builder
    {
        return $query->where('is_billable', 0);
    }
}
