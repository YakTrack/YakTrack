<?php

namespace App\Models\Collections;

use App\Support\DateIntervalFormatter;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * @extends EloquentCollection<int, \App\Models\Session>
 */
class SessionCollection extends EloquentCollection
{
    public function totalDurationInSeconds(): int
    {
        return $this->sum->durationInSeconds;
    }

    public function totalDurationInHours(): float
    {
        return $this->sum->durationInHours;
    }

    public function totalDurationForHumans(): string
    {
        $dateTimeFormatter = app(DateIntervalFormatter::class);

        return $dateTimeFormatter->forHumans(
            $dateTimeFormatter->createFromSeconds(
                $this->totalDurationInSeconds()
            )
        );
    }

    public function whereThisWeek(): SessionCollection
    {
        return $this->filter(function ($session) {
            return $session->isThisWeek();
        });
    }

    /**
     * Deprecated in favour of whereThisWeek.
     */
    public function thisWeek(): SessionCollection
    {
        return $this->whereThisWeek();
    }

    public function whereBillable(): SessionCollection
    {
        return $this->filter(function ($session) {
            return $session->is_billable;
        });
    }

    public function whereNotBillable(): SessionCollection
    {
        return $this->filter(function ($session) {
            return !$session->is_billable;
        });
    }
}
