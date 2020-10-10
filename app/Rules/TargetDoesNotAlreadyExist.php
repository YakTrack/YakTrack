<?php

namespace App\Rules;

use App\Models\Target;
use Illuminate\Contracts\Validation\Rule;

class TargetDoesNotAlreadyExist implements Rule
{
    protected $duration;

    protected $duration_unit;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $requestedTargetDetails)
    {
        $this->duration = $requestedTargetDetails['duration'];
        $this->duration_unit = $requestedTargetDetails['duration_unit'];
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !Target::where([
            'duration' => $this->duration,
            'duration_unit' => $this->duration_unit,
            'starts_at' => $value,
        ])->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'A target of that type already exists for that time period.';
    }
}
