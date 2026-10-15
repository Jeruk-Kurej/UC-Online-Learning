<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Fields shared by the admin create-user and edit-user forms. The routes sit behind the admin middleware.
 */
abstract class UserFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // --- Rule groups (kept in form order so error messages keep their order) ---

    protected function accountRules(string $email, array $password): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => $email,
            'password' => $password,
            'role' => 'required|in:user,admin',
            'student_status' => 'required_if:role,user|nullable|string',
        ];
    }

    protected function profileRules(): array
    {
        return [
            // Identity & Contact
            'prefix_title' => 'nullable|string|max:255',
            'suffix_title' => 'nullable|string|max:255',
            'personal_email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'mobile_number' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'linkedin' => 'nullable|string|max:255',

            // Academic & Career
            'nis' => 'nullable|string|max:255',
            'year_of_enrollment' => 'nullable|string|max:50',
            'graduate_year' => 'nullable|string|max:50',
            'major' => 'nullable|string|max:255',
            'current_status' => 'nullable|string|max:255',
            'testimony' => 'nullable|string',
            'activities_caption' => 'nullable|string',
            'show_contact_details' => 'nullable|boolean',
        ];
    }

    protected function ownershipRules(): array
    {
        return [
            'is_visible' => 'nullable|boolean',
            'owned_businesses' => 'nullable|array',
            'owned_businesses.*' => 'exists:businesses,id',
        ];
    }

    // --- Profile columns taken from the form (empty fields are stored as null; admins get the default status) ---

    public function profileData(): array
    {
        $validated = $this->validated();
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'student_status' => $validated['role'] === 'admin' ? 'student aktif' : ($validated['student_status'] ?? 'student aktif'),
        ];

        foreach (['prefix_title', 'suffix_title', 'personal_email', 'phone_number', 'mobile_number', 'whatsapp', 'linkedin',
            'nis', 'year_of_enrollment', 'graduate_year', 'major', 'current_status', 'testimony', 'activities_caption'] as $field) {
            $data[$field] = $validated[$field] ?? null;
        }

        return $data;
    }
}
