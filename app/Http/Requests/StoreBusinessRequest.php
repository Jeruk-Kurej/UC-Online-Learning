<?php

namespace App\Http\Requests;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Business::class);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'products' => $this->normalizeInlineRows($this->input('products', []), 'product'),
            'services' => $this->normalizeInlineRows($this->input('services', []), 'service'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Basic fields
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'category_id' => 'required|exists:categories,id',
            'business_mode' => 'required|in:product,service,both',
            'user_id' => 'nullable|exists:users,id',
            'owner_ids' => 'nullable|array',
            'owner_ids.*' => 'integer|exists:users,id',
            'position' => 'nullable|string|max:255',

            // Location
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'address' => 'nullable|string',

            // Enhanced fields
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'established_date' => 'nullable|date',
            'employee_count' => 'nullable|integer|min:0',
            'revenue_range' => 'nullable|string|max:255',
            'is_from_college_project' => 'nullable|boolean|string',
            'is_continued_after_graduation' => 'nullable|boolean|string',
            'legal_document_path' => 'nullable|file|mimes:pdf|max:5120',
            'certification_path' => 'nullable|file|mimes:pdf|max:5120',
            'legal_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'product_certifications.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            // Additional data fields
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'instagram_handle' => 'nullable|string|max:100',
            'whatsapp_number' => 'nullable|string|max:50',
            'product_name' => 'nullable|string|max:255',
            'product_description' => 'nullable|string|max:2000',
            'unique_value_proposition' => 'nullable|string|max:1000',
            'target_market' => 'nullable|string|max:255',
            'customer_base_size' => 'nullable|integer|min:0',
            'establishment_date' => 'nullable|date',
            'operational_status' => 'nullable|in:active,inactive,seasonal',

            // Inline products/services
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

    /**
     * Add "after" validation hooks.
     */
    public function after(): array
    {
        return [
            function ($validator) {


                $user = $this->user();

                // Admin validation for owner/user
                if ($user->isAdmin()) {
                    $selectedOwnerIds = collect($this->input('owner_ids', []))
                        ->map(fn ($id) => (int) $id)
                        ->filter(fn ($id) => $id > 0)
                        ->unique()
                        ->values()
                        ->all();

                    if (!empty($selectedOwnerIds)) {
                        $adminOwnerExists = User::whereIn('id', $selectedOwnerIds)
                            ->where('role', 'admin')
                            ->exists();

                        if ($adminOwnerExists) {
                            $validator->errors()->add('owner_ids', 'Admin UCO tidak boleh menjadi owner business.');
                        }
                    }

                    if ($this->filled('user_id')) {
                        $ownerUser = User::find($this->input('user_id'));
                        if ($ownerUser?->role === 'admin') {
                            $validator->errors()->add('user_id', 'Admin UCO tidak boleh menjadi owner business.');
                        }
                    }
                }

                // Business mode logical checks vs inline products/services
                $mode = $this->input('business_mode');
                $productRows = $this->input('products', []);
                $serviceRows = $this->input('services', []);

                $inlineErrors = array_merge(
                    $this->validateInlineRows($productRows, 'product'),
                    $this->validateInlineRows($serviceRows, 'service')
                );

                if ($mode === 'service' && !empty($productRows)) {
                    $inlineErrors['products'] = 'Business mode Service Only tidak boleh memiliki produk.';
                }
                if ($mode === 'product' && !empty($serviceRows)) {
                    $inlineErrors['services'] = 'Business mode Product Only tidak boleh memiliki layanan.';
                }

                foreach ($inlineErrors as $key => $message) {
                    $validator->errors()->add($key, $message);
                }
            }
        ];
    }

    /**
     * Normalize rows from inline form arrays.
     */
    private function normalizeInlineRows($rows, string $type): array
    {
        if (!is_array($rows)) {
            return [];
        }

        $normalized = [];

        foreach ($rows as $row) {
            if (!is_array($row)) {
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

            if (!$isFilled) {
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

    /**
     * Validate inline rows locally to populate errors.
     */
    private function validateInlineRows(array $rows, string $type): array
    {
        $errors = [];

        foreach ($rows as $index => $row) {
            if (empty($row['name'])) {
                $errors["{$type}s.{$index}.name"] = ucfirst($type) . ' #' . ($index + 1) . ': nama wajib diisi.';
            }

            if (empty($row['description'])) {
                $errors["{$type}s.{$index}.description"] = ucfirst($type) . ' #' . ($index + 1) . ': deskripsi wajib diisi.';
            }

            if ($row['price'] === null || $row['price'] === '') {
                $errors["{$type}s.{$index}.price"] = ucfirst($type) . ' #' . ($index + 1) . ': harga wajib diisi.';
            }

            if ($type === 'service' && empty($row['price_type'])) {
                $errors["services.{$index}.price_type"] = 'Service #' . ($index + 1) . ': tipe harga wajib diisi.';
            }
        }

        return $errors;
    }
}
