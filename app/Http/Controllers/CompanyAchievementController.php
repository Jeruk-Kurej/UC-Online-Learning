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
    public function create(Company $company): View
    {
        $this->authorizeManage($company);

        return view('work-profiles.add-achievement', compact('company'));
    }

    public function store(Company $company, Request $request): RedirectResponse|JsonResponse
    {
        $this->authorizeManage($company);

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
     * Deletes the achievement at `index`, but only when it still reads `text`: a second tab or a stale page must not delete the item that moved into its place.
     */
    public function destroy(Company $company, Request $request): RedirectResponse|JsonResponse
    {
        $this->authorizeManage($company);

        $this->validate($request, [
            'index' => 'required|integer|min:0',
            'text' => 'nullable|string',
        ]);

        $achievements = $company->achievements_list;
        $index = (int) $request->input('index');
        $expected = $request->input('text');
        $found = isset($achievements[$index]) && ($expected === null || $achievements[$index] === $expected);

        if ($found) {
            unset($achievements[$index]);
            $company->achievement = empty($achievements) ? null : implode("\n", array_map(fn ($item) => '- '.$item, $achievements));
            $company->save();
        }

        $message = $found ? 'Achievement deleted successfully!' : 'This list changed in the meantime. Nothing was deleted; check the list and try again.';

        if ($request->ajax() || $request->wantsJson()) {
            return new JsonResponse([
                'success' => $found,
                'message' => $message,
                'achievements' => $company->achievements_list ?? [],
            ]);
        }

        return back()->with($found ? 'success' : 'error', $message);
    }

    // --- Access ---

    private function authorizeManage(Company $company): void
    {
        abort_unless($company->canBeManagedBy(Auth::user()), 403, 'Unauthorized.');
    }
}
