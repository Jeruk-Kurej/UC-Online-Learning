<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;

    // --- Request values ---

    /**
     * A filter or search value as text. A list sent in its place (`?search[]=x`, as bots do) counts as not given, instead of breaking the page.
     */
    protected function textInput(Request $request, string $key, string $default = ''): string
    {
        $value = $request->input($key, $default);

        return is_scalar($value) ? (string) $value : $default;
    }

    /**
     * Several of them at once, for the filter fields of a list page.
     *
     * @return array<string, string>
     */
    protected function textInputs(Request $request, array $keys): array
    {
        return collect($keys)->mapWithKeys(fn (string $key) => [$key => $this->textInput($request, $key)])->all();
    }

    // --- Current user ---

    protected function currentUserIsAdmin(): bool
    {
        return (bool) request()->user()?->isAdmin();
    }

    /**
     * Where Cancel on a business or work profile form leads: the admin list for admins, "my businesses" for everyone else.
     */
    protected function venturesHomeUrl(): string
    {
        return $this->currentUserIsAdmin() ? route('businesses.admin') : route('businesses.my');
    }
}
