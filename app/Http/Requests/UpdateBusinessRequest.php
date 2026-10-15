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

    // --- A business cannot switch to services only while it still has products ---

    protected function checkBeforeSharedRules($validator): void
    {
        $business = $this->route('business');

        $mode = $this->input('business_mode');
        $hasProducts = $business->products()->count() > 0;

        if ($mode !== $business->offering_type) {
            if ($mode === 'service' && $hasProducts) {
                $validator->errors()->add('business_mode', 'Cannot change to Service Only while products exist. Delete products first or choose "Product & Service".');
            }
        }
    }
}
