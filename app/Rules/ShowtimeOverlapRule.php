<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class ShowtimeOverlapRule implements ValidationRule, DataAwareRule
{
    public function __construct(
        protected $id,
        protected $availabilityService,
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
        // If the submitted request does not have all the necessary data
        if(!Arr::hasAll($this->data, ['movie', 'theater', 'date', 'screen', 'time', 'subtitles', 'is_3d', 'dubbed'])){
            return;
        }
        // If any of the values are missing
        foreach($this->data as $value){
            if(is_null($value)){
                return;
            }
        }

        $overlapExists = $this->availabilityService->validateOverlap($this->data, $this->id);

        // If there is overlap
        if($overlapExists){
            $fail('The selected timeslot is taken.');
        }
    }
}
