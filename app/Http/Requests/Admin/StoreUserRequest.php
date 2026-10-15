<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rules;

class StoreUserRequest extends UserFormRequest
{
    public function rules(): array
    {
        return $this->accountRules('required|string|email|max:255|unique:users', ['required', 'confirmed', Rules\Password::defaults()])
            + $this->profileRules()
            + [
                'profile_photo_url' => 'nullable|image|max:5120',
                'activities_doc_url' => 'nullable|mimes:pdf|max:10240',
            ]
            + $this->ownershipRules();
    }
}
