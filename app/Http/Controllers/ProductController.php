<?php

namespace App\Http\Controllers;

use App\Models\Business;

class ProductController extends OfferingController
{
    protected function type(): string
    {
        return 'product';
    }

    protected function offersType(Business $business): bool
    {
        return $business->isProductMode();
    }
}
