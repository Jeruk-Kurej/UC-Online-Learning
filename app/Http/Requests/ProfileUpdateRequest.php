<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'prefix_title'       => ['nullable', 'string', 'max:255'],
            'name'               => ['required', 'string', 'max:255'],
            'suffix_title'       => ['nullable', 'string', 'max:255'],
            'personal_email'     => ['nullable', 'email', 'max:255'],
            'phone_number'       => ['nullable', 'string', 'max:50'],
            'mobile_number'      => ['nullable', 'string', 'max:50'],
            'whatsapp'           => ['nullable', 'string', 'max:50'],
            'linkedin'           => ['nullable', 'string', 'max:255'],
            'current_status'     => ['nullable', 'string', 'max:255'],
            'year_of_enrollment' => ['nullable', 'string', 'max:50'],
            'graduate_year'      => ['nullable', 'string', 'max:50'],
            'testimony'          => ['nullable', 'string'],
            'profile_photo'      => ['nullable', 'image', 'max:10240'],
            'cv_file'            => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
            'activities_file'    => ['nullable', 'file', 'max:50120'],
            'password'           => ['nullable', 'confirmed', 'min:8'],
        ];
    }
}
