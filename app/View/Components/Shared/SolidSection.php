<?php

declare(strict_types=1);

namespace App\View\Components\Shared;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SolidSection extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $text = null,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.shared.solid-section');
    }
}
