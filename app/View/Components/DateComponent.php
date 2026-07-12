<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DateComponent extends Component
{
    public function __construct(
        public $date = null,
        public string $format = 'M j, Y',
    ) {}

    public function render()
    {
        return function () {
            if (!$this->date) return '';
            $formatted = $this->date instanceof \Carbon\Carbon
                ? $this->date->format($this->format)
                : \Carbon\Carbon::parse($this->date)->format($this->format);
            return $formatted;
        };
    }
}
