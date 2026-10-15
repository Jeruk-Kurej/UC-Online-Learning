<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rules;

class UpdateUserRequest extends UserFormRequest
{
    public function rules(): array
    {
        return $this->accountRules('required|string|email|max:255|unique:users,email,'.$this->route('user')->id, ['nullable', 'confirmed', Rules\Password::defaults()])
            + $this->profileRules()
            + [
                // Files & Deletion flags
                'profile_photo_url' => 'nullable|image|max:5120',
                'delete_profile_photo_url' => 'nullable|boolean',
                'activities_docs' => ['nullable', 'array'],
                'activities_docs.*' => ['file', 'mimes:pdf,jpeg,png,jpg,webp', 'max:10240'],
                'delete_activities_files' => ['nullable', 'array'],
                'delete_activities_files.*' => ['string'],
            ]
            + $this->ownershipRules();
    }
}
