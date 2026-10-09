<?php

declare(strict_types=1);

namespace App\View\Components\Shared;

use App\Settings\SiteSettings;
use App\Settings\SocialMediaSettings;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public SiteSettings $settings,
        public SocialMediaSettings $social,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.shared.footer');
    }
}
