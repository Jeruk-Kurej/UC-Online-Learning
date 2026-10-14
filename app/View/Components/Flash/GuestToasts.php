<?php

namespace App\View\Components\Flash;

use Illuminate\Support\ViewErrorBag;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Toast stack of the login page and the guest layout, pre-filled from the session.
 */
class GuestToasts extends Component
{
    /** @var array<int, array{message: string, type: string}> */
    public array $initial = [];

    public function __construct()
    {
        foreach (['success', 'error', 'status'] as $type) {
            if (session($type)) {
                $this->initial[] = ['message' => (string) session($type), 'type' => $type];
            }
        }

        $errors = session('errors');
        if ($errors instanceof ViewErrorBag) {
            foreach ($errors->all() as $error) {
                $this->initial[] = ['message' => $error, 'type' => 'error'];
            }
        }
    }

    public function render(): View
    {
        return view('components.flash.guest-toasts');
    }
}
