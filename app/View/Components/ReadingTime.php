<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ReadingTime extends Component
{
    public function __construct(
        public int $minutes = 1,
    ) {}

    public function render()
    {
        return function () {
            return "{$this->minutes} min read";
        };
    }
}
