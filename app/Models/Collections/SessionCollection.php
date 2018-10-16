<?php

namespace App\Models\Collections;

use App\Support\DateIntervalFormatter;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class SessionCollection extends EloquentCollection
{
    public function totalDurationInSeconds()
    {
        return $this->sum(function ($session) {
            return $session->durationInSeconds;
        });
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
}
