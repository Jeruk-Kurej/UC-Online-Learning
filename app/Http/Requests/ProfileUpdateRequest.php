<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * The password changes only through the password form (it asks for the current one), the testimony only through My testimony.
     */
    public function rules(): array
    {
        return [
            'prefix_title' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'suffix_title' => ['nullable', 'string', 'max:255'],
            'personal_email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'mobile_number' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'linkedin' => ['nullable', 'string', 'max:255'],
            'show_contact_details' => ['nullable', 'boolean'],
            'current_status' => ['nullable', 'string', 'max:255'],
            'year_of_enrollment' => ['nullable', 'string', 'max:50'],
            'graduate_year' => ['nullable', 'string', 'max:50'],
            'activities_caption' => ['nullable', 'string'],
            'profile_photo' => ['nullable', 'image', 'max:10240'],
            'delete_profile_photo' => ['nullable', 'boolean'],

            'activities_files' => ['nullable', 'array'],
            'activities_files.*' => ['file', 'mimes:pdf,jpeg,png,jpg,webp', 'max:50120'],
            'delete_activities_files' => ['nullable', 'array'],
            'delete_activities_files.*' => ['string'],

            'skills' => ['nullable', 'string', 'max:1000'],
            'certification_files' => ['nullable', 'array'],
            'certification_files.*' => ['file', 'mimes:pdf,jpeg,png,jpg,webp', 'max:10240'],
            'delete_certification_files' => ['nullable', 'array'],
            'delete_certification_files.*' => ['string'],
        ];
    }
}
