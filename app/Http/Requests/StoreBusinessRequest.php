<?php

namespace App\Http\Requests;

use App\Models\Business;

class StoreBusinessRequest extends BusinessFormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Business::class);
    }

    protected function maxLogoKilobytes(): int
    {
        return 10240;
    }

    protected function inlineTypes(): array
    {
        return ['product', 'service'];
    }
}
