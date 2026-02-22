<?php

namespace App\Rules;

use App\Services\Showtime\ShowtimeAvailabilityService;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class ShowtimeOverlapRule implements ValidationRule, DataAwareRule
{
    public function __construct(
        protected ShowtimeAvailabilityService $availabilityService,
    )
    {
        //
    }
    protected array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Checks for showtime overlap
     *
     * @param string $attribute (time)
     * @param mixed $value - The value from the time input (H:i)
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $overlapExists = $this->availabilityService->validateOverlap($this->data);

        // If there is overlap
        if($overlapExists){
            $fail('The selected timeslot is taken.');
        }
    }
}
