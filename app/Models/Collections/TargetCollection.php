<?php

namespace App\Models\Collections;

use App\Models\Target;
use App\Support\DateIntervalFormatter;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * @extends EloquentCollection<int, Target>
 */
class TargetCollection extends EloquentCollection
{
    public function totalValueInSeconds(): int
    {
        return $this->sum->valueInSeconds();
    }

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

    public function thisWeek(): self
    {
        return $this->filter(function ($target) {
            return $target->isThisWeek();
        });
    }
}
