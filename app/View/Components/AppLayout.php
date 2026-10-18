<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * A page that sets its own width (a full-width band at the top) passes bleed; every other page sits in the shared width.
     */
    public function __construct(public bool $bleed = false) {}

    public function render(): View
    {
        return view('layouts.app');
    }
}
