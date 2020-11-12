<?php

namespace App\Models\Collections;

use App\Support\DateIntervalFormatter;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class SessionCollection extends EloquentCollection
{
    public function totalDurationInSeconds()
    {
        return $this->sum->durationInSeconds;
    }

    public function totalDurationInHours()
    {
        return $this->sum->durationInHours;
    }

    public function totalDurationForHumans()
    {
        $dateTimeFormatter = app(DateIntervalFormatter::class);

        return $dateTimeFormatter->forHumans(
            $dateTimeFormatter->createFromSeconds(
                $this->totalDurationInSeconds()
            )
        );
    }

    public function thisWeek()
    {
        return $this->filter(function ($session) {
            return $session->isThisWeek();
        });
    }

    public function whereBillable()
    {
        return $this->filter(function ($session) {
            return $session->is_billable;
        });
    }

    public function whereNotBillable()
    {
        return $this->filter(function ($session) {
            return !$session->is_billable;
        });
    }
}
