<?php

namespace App\Models\Concerns;

trait CanBeBillable
{
    public function scopeWhereBillable($query)
    {
        return $query->where('is_billable', 1);
    }

    public function scopeWhereNotBillable($query)
    {
        return $query->where('is_billable', 0);
    }
}