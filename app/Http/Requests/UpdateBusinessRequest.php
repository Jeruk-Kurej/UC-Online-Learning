<?php

namespace App\Http\Requests;

class UpdateBusinessRequest extends BusinessFormRequest
{
    public function authorize(): bool
    {
        $business = $this->route('business');

        return $this->user()->can('update', $business);
    }

    protected function maxLogoKilobytes(): int
    {
        return 20480;
    }

    protected function inlineTypes(): array
    {
        return ['product'];
    }

    protected function logoFlagRules(): array
    {
        return [
            'delete_logo' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ];
    }

    protected function documentRemovalRules(): array
    {
        return [
            'remove_legal_docs' => 'nullable|array',
            'remove_certifications' => 'nullable|array',
            'remove_legal_document' => 'nullable|boolean',
            'remove_certification' => 'nullable|boolean',
        ];
    }

    // --- A business cannot drop a kind of offering it still has: services only while products exist, or products only while services exist ---

    protected function checkBeforeSharedRules($validator): void
    {
        $business = $this->route('business');
        $mode = $this->input('business_mode');

        if ($mode === $business->offering_type) {
            return;
        }

        if ($mode === 'service' && $business->products()->where('type', 'product')->exists()) {
            $validator->errors()->add('business_mode', 'This business still has products. Delete them first, or choose "Products and services".');
        }

        if ($mode === 'product' && $business->products()->where('type', 'service')->exists()) {
            $validator->errors()->add('business_mode', 'This business still has services. Delete them first, or choose "Products and services".');
        }
    }
}
