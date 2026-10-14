<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Achievements listed on a work profile.
 */
class CompanyAchievementController extends Controller
{
    /**
     * Show the form for creating a new company achievement.
     */
    public function create(Company $company): View
    {
        if (Auth::id() !== $company->user_id && ! $this->currentUserIsAdmin()) {
            abort(403, 'Unauthorized.');
        }

        return view('businesses.add_achievement', compact('company'));
    }

    /**
     * Add achievement to a company.
     */
    public function store(Company $company, Request $request): RedirectResponse|JsonResponse
    {
        if (Auth::id() !== $company->user_id && ! $this->currentUserIsAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $this->validate($request, [
            'achievement' => 'required|string|max:500',
        ]);

        $newAchievement = trim((string) $request->input('achievement'));

        $existing = trim($company->achievement);
        if (empty($existing)) {
            $updated = '- '.$newAchievement;
        } else {
            $updated = $existing."\n- ".$newAchievement;
        }

        $company->achievement = $updated;
        $company->save();

        if ($request->ajax() || $request->wantsJson()) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Achievement added successfully!',
                'achievements' => $company->achievements_list,
            ]);
        }

        return redirect()->route('intrapreneurs.show', $company)->with('success', 'Achievement added successfully!');
    }

    /**
     * Delete achievement from a company.
     */
    public function destroy(Company $company, Request $request): RedirectResponse|JsonResponse
    {
        if (Auth::id() !== $company->user_id && ! $this->currentUserIsAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $this->validate($request, [
            'index' => 'required|integer|min:0',
        ]);

        $achievements = $company->achievements_list;
        $index = (int) $request->input('index');

        if (isset($achievements[$index])) {
            unset($achievements[$index]);
            $updated = empty($achievements) ? null : implode("\n", array_map(fn ($item) => '- '.$item, $achievements));
            $company->achievement = $updated;
            $company->save();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Achievement deleted successfully!',
                'achievements' => $company->achievements_list ?? [],
            ]);
        }

        return back()->with('success', 'Achievement deleted successfully!');
    }
}
