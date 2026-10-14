<?php

namespace App\Http\Controllers;

use App\Models\Business;

class ServiceController extends OfferingController
{
    protected function type(): string
    {
        return 'service';
    }

    protected function offersType(Business $business): bool
    {
        return $business->isServiceMode();
    }

    protected function photoRequiredOnCreate(): bool
    {
        return true;
    }
}
