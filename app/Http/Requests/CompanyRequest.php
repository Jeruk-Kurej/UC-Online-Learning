<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Register or edit a work profile (intrapreneur company). Editing is for its owner and admins.
 */
class CompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = $this->route('company');

        if ($company && ! $company->canBeManagedBy($this->user())) {
            abort(403);
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'position' => 'required|string|max:255',
            'level_position' => 'nullable|string|max:255',
            'company_scale' => 'nullable|string|max:255',
            'province' => 'nullable',
            'city' => 'nullable',
            'year_started_working' => 'nullable|integer|min:1900|max:'.(date('Y') + 1),
            'job_description' => 'required|string|min:10',
            'logo_url' => 'nullable|image|max:5120',
        ];
    }
}
