<?php

namespace App\Models\Collections;

use App\Support\DateIntervalFormatter;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class TargetCollection extends EloquentCollection
{
    public function totalValueInSeconds()
    {
        return $this->sum->valueInSeconds();
    }

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
        return $this->filter(function ($target) {
            return $target->isThisWeek();
        });
    }
}
