<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * A page that sets its own width (a full-width band at the top) passes bleed; wide pages (admin tables) get a wider shared width, the header and the footer included; every other page sits in the shared width.
     */
    public function __construct(public bool $bleed = false, public bool $wide = false) {}

    public function render(): View
    {
        return view('layouts.app');
    }
}
