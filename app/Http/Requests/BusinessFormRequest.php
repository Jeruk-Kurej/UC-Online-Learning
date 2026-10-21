<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Rules shared by the register-business and edit-business forms.
 */
abstract class BusinessFormRequest extends FormRequest
{
    // --- What differs between creating and editing ---

    abstract protected function maxLogoKilobytes(): int;

    protected function logoFlagRules(): array
    {
        return [];
    }

    /** Checks that run before the shared ones (editing checks the mode change). */
    protected function checkBeforeSharedRules($validator): void {}

    // --- Form field names that differ from the column names (an empty field keeps its form name, which fill() ignores) ---

    private const COLUMNS = [
        'business_mode' => 'offering_type',
        'phone' => 'phone_number',
        'whatsapp_number' => 'whatsapp',
        'instagram_handle' => 'instagram',
    ];

    public function businessData(): array
    {
        $validated = $this->validated();
        $data = $validated;

        foreach (self::COLUMNS as $field => $column) {
            if (isset($validated[$field])) {
                $data[$column] = $validated[$field];
                unset($data[$field]);
            }
        }

        return $data;
    }

    // --- Validation ---

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // --- Basic fields ---
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'category_id' => 'required|exists:categories,id',
            'business_mode' => 'required|in:product,service,both',
            'user_id' => [Rule::requiredIf(fn () => $this->user()->isAdmin()), 'nullable', 'exists:users,id'],
            'owner_ids' => 'nullable|array',
            'owner_ids.*' => 'integer|exists:users,id',
            'position' => 'nullable|string|max:255',

            // --- Location ---
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'address' => 'nullable|string',

            // --- Logo and business facts ---
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:'.$this->maxLogoKilobytes(),
        ] + $this->logoFlagRules() + [
            'established_date' => 'nullable|date',
            'employee_count' => 'nullable|integer|min:0',
            'revenue_range' => 'nullable|string|max:255',

            // --- Contact and market details ---
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'instagram_handle' => 'nullable|string|max:100',
            'whatsapp_number' => 'nullable|string|max:50',
            'unique_value_proposition' => 'nullable|string|max:1000',
            'target_market' => 'nullable|string|max:255',
            'business_scale' => 'nullable|string|max:255',
            'business_legality' => 'nullable|string|max:255',
            'product_legality' => 'nullable|string|max:255',
            'customer_base_size' => 'nullable|integer|min:0',
            'operational_status' => 'nullable|in:active,inactive,seasonal',
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                $this->checkBeforeSharedRules($validator);
                $this->checkOwnersAreNotAdmins($validator);
            },
        ];
    }

    // --- Admins pick the owners, but an admin account can never own a business ---

    private function checkOwnersAreNotAdmins($validator): void
    {
        if (! $this->user()->isAdmin()) {
            return;
        }

        $selectedOwnerIds = collect($this->input('owner_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        if (! empty($selectedOwnerIds) && User::whereIn('id', $selectedOwnerIds)->where('role', 'admin')->exists()) {
            $validator->errors()->add('owner_ids', 'An admin account cannot own or run a business. Choose a student.');
        }

        if ($this->filled('user_id') && User::find($this->input('user_id'))?->isAdmin()) {
            $validator->errors()->add('user_id', 'An admin account cannot own or run a business. Choose a student.');
        }
    }
}
