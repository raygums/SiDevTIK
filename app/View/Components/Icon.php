<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Icon extends Component
{
    public string $name;
    public string $class;

    public function __construct(string $name, string $class = 'h-5 w-5')
    {
        $this->name = $name;
        $this->class = $class;
    }

    public function render()
    {
        return view('components.icon');
    }
}
