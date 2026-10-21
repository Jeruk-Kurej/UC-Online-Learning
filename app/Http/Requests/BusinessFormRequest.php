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

    /** Inline row lists cleaned up before validation. */
    abstract protected function inlineTypes(): array;

    protected function logoFlagRules(): array
    {
        return [];
    }

    protected function documentRemovalRules(): array
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

    protected function prepareForValidation(): void
    {
        $rows = [];
        foreach ($this->inlineTypes() as $type) {
            $rows[$type.'s'] = $this->normalizeInlineRows($this->input($type.'s', []), $type);
        }
        $this->merge($rows);
    }

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

            // --- Logo, documents and business facts ---
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:'.$this->maxLogoKilobytes(),
        ] + $this->logoFlagRules() + [
            'established_date' => 'nullable|date',
            'employee_count' => 'nullable|integer|min:0',
            'revenue_range' => 'nullable|string|max:255',
            'is_from_college_project' => 'nullable|boolean|string',
            'is_continued_after_graduation' => 'nullable|boolean|string',
            'legal_document_path' => 'nullable|file|mimes:pdf|max:5120',
            'certification_path' => 'nullable|file|mimes:pdf|max:5120',
            'legal_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'product_certifications.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ] + $this->documentRemovalRules() + [
            // --- Contact and market details ---
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'instagram_handle' => 'nullable|string|max:100',
            'whatsapp_number' => 'nullable|string|max:50',
            'product_name' => 'nullable|string|max:255',
            'product_description' => 'nullable|string|max:2000',
            'unique_value_proposition' => 'nullable|string|max:1000',
            'target_market' => 'nullable|string|max:255',
            'business_scale' => 'nullable|string|max:255',
            'business_legality' => 'nullable|string|max:255',
            'product_legality' => 'nullable|string|max:255',
            'customer_base_size' => 'nullable|integer|min:0',
            'establishment_date' => 'nullable|date',
            'operational_status' => 'nullable|in:active,inactive,seasonal',

            // --- Inline products / services ---
            'products' => 'nullable|array',
            'products.*.id' => 'nullable|integer',
            'products.*.name' => 'nullable|string|max:255',
            'products.*.description' => 'nullable|string|max:2000',
            'products.*.price' => 'nullable|numeric|min:0',
            'services' => 'nullable|array',
            'services.*.id' => 'nullable|integer',
            'services.*.name' => 'nullable|string|max:255',
            'services.*.description' => 'nullable|string|max:2000',
            'services.*.price_type' => 'nullable|string|max:255',
            'services.*.price' => 'nullable|numeric|min:0',
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                $this->checkBeforeSharedRules($validator);
                $this->checkOwnersAreNotAdmins($validator);
                $this->checkInlineOfferings($validator);
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

    // --- Inline products / services: every row complete, and none of a kind the business mode rules out ---

    private function checkInlineOfferings($validator): void
    {
        $mode = $this->input('business_mode');
        $productRows = $this->input('products', []);
        $serviceRows = $this->input('services', []);

        $inlineErrors = array_merge(
            $this->validateInlineRows($productRows, 'product'),
            $this->validateInlineRows($serviceRows, 'service')
        );

        if ($mode === 'service' && ! empty($productRows)) {
            $inlineErrors['products'] = 'Business mode Service Only tidak boleh memiliki produk.';
        }
        if ($mode === 'product' && ! empty($serviceRows)) {
            $inlineErrors['services'] = 'Business mode Product Only tidak boleh memiliki layanan.';
        }

        foreach ($inlineErrors as $key => $message) {
            $validator->errors()->add($key, $message);
        }
    }

    private function normalizeInlineRows($rows, string $type): array
    {
        if (! is_array($rows)) {
            return [];
        }

        $normalized = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $name = isset($row['name']) ? trim((string) $row['name']) : '';
            $description = isset($row['description']) ? trim((string) $row['description']) : '';
            $priceRaw = $row['price'] ?? null;
            $price = ($priceRaw === '' || $priceRaw === null) ? null : $priceRaw;
            $id = isset($row['id']) && is_numeric($row['id']) ? (int) $row['id'] : null;

            $isFilled = $name !== '' || $description !== '' || $price !== null;
            if ($type === 'service') {
                $priceType = isset($row['price_type']) ? trim((string) $row['price_type']) : '';
                $isFilled = $isFilled || $priceType !== '';
            }

            if (! $isFilled) {
                continue;
            }

            $payload = [
                'id' => $id,
                'name' => $name,
                'description' => $description,
                'price' => $price,
            ];

            if ($type === 'service') {
                $payload['price_type'] = isset($row['price_type']) ? trim((string) $row['price_type']) : '';
            }

            $normalized[] = $payload;
        }

        return $normalized;
    }

    private function validateInlineRows(array $rows, string $type): array
    {
        $errors = [];

        foreach ($rows as $index => $row) {
            if (empty($row['name'])) {
                $errors["{$type}s.{$index}.name"] = ucfirst($type).' #'.($index + 1).': nama wajib diisi.';
            }

            if (empty($row['description'])) {
                $errors["{$type}s.{$index}.description"] = ucfirst($type).' #'.($index + 1).': deskripsi wajib diisi.';
            }

            if ($row['price'] === null || $row['price'] === '') {
                $errors["{$type}s.{$index}.price"] = ucfirst($type).' #'.($index + 1).': harga wajib diisi.';
            }

            if ($type === 'service' && empty($row['price_type'])) {
                $errors["services.{$index}.price_type"] = 'Service #'.($index + 1).': tipe harga wajib diisi.';
            }
        }

        return $errors;
    }
}
