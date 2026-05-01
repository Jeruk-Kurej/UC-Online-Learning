<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            // role should match create form values
            'role' => 'required|in:student,alumni,admin',
            'is_visible' => 'boolean',
            
            // CSV: Identity
            'prefix_title' => 'nullable|string|max:255',
            'suffix_title' => 'nullable|string|max:255',
            'personal_email' => 'nullable|email|max:255',
            
            // CSV: Contact
            'phone_number' => 'nullable|string|max:50',
            'mobile_number' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'linkedin' => 'nullable|string|max:255',
            
            // CSV: Academic
            'nis' => 'nullable|string|max:50',
            'year_of_enrollment' => 'nullable|string|max:50',
            'graduate_year' => 'nullable|string|max:50',
            'major' => 'nullable|string|max:255',
            'current_status' => 'nullable|string|max:255',

            // CSV: Profile extras
            'testimony' => 'nullable|string|max:2000',
            'student_status' => 'nullable|in:active,inactive,cuti,alumni',
        ];
    }
}
