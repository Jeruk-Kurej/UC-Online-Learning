<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\QueuesImports;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Admin moderation of businesses and work profiles: lists, approval, featuring, deletion and import.
 */
class BusinessController extends Controller
{
    use QueuesImports;

    public function index(Request $request): View|Response
    {
        $viewType = $request->get('type') === 'intrapreneur' ? 'intrapreneur' : 'entrepreneur';
        $businesses = $this->filteredList($request, $viewType)->newestFirst()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()
                ->view('businesses.admin.partials.list', compact('businesses', 'viewType'))
                ->header('Vary', 'X-Requested-With');
        }

        return view('businesses.admin.index', [
            'businesses' => $businesses,
            'viewType' => $viewType,
            'status' => $request->get('status'),
            'search' => $request->get('search'),
            'featured' => $request->get('featured'),
        ] + $this->listStats($viewType));
    }

    public function updateStatus(Request $request, Business $business): RedirectResponse
    {
        $status = $this->validatedStatus($request);
        $business->applyApprovalStatus($status, $request->input('rejection_reason'));

        return back()->with('success', 'Business status updated to '.ucfirst(str_replace('_', ' ', (string) $status)));
    }

    public function updateCompanyStatus(Request $request, Company $company): RedirectResponse
    {
        $status = $this->validatedStatus($request);
        $company->applyApprovalStatus($status, $request->input('rejection_reason'));

        return back()->with('success', 'Company status updated to '.ucfirst(str_replace('_', ' ', (string) $status)));
    }

    public function approve(Business $business): RedirectResponse
    {
        $business->is_visible = true;
        $business->save();

        return back()->with('success', "Business \"{$business->name}\" has been approved and is now visible.");
    }

    public function toggleFeatured(Request $request, Business $business): RedirectResponse|JsonResponse
    {
        if ($business->approval_status !== 'approved') {
            return $this->reply($request, 'Only approved businesses can be featured.', false);
        }

        $business->is_featured = ! $business->is_featured;
        $business->save();

        return $this->reply($request, $business->is_featured
            ? "\"{$business->name}\" is now featured."
            : "\"{$business->name}\" removed from featured.");
    }

    public function import(Request $request): RedirectResponse|JsonResponse
    {
        $this->validate($request, [
            'file' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        try {
            [$importId, $format] = $this->queueImport($request->file('file'));

            session(['active_import' => $importId]);

            if ($request->wantsJson()) {
                return $this->importQueuedJson($importId, $format, "Import queued! Format: {$format}.");
            }

            return back()->with('success', "Import queued! Format: {$format}. Processing ~1500 rows in background...")
                ->with('importId', $importId);
        } catch (\Exception $e) {
            Log::error('Import error: '.$e->getMessage());
            if ($request->wantsJson()) {
                return $this->importFailedJson($e);
            }

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // --- List and statistics (entrepreneur businesses or intrapreneur work profiles) ---

    private function filteredList(Request $request, string $viewType): Builder
    {
        $query = $viewType === 'intrapreneur'
            ? Company::with(['user', 'category'])
            : Business::with(['user', 'category'])->entrepreneur();

        if ($status = $request->get('status')) {
            $query->where('approval_status', $status);
        }

        if ($viewType === 'entrepreneur' && in_array($request->get('featured'), ['yes', 'no'], true)) {
            $query->where('is_featured', $request->get('featured') === 'yes');
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search, $viewType) {
                $q->where('name', 'LIKE', "%{$search}%");
                if ($viewType === 'intrapreneur') {
                    $q->orWhere('position', 'LIKE', "%{$search}%");
                }
                $q->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        return $query;
    }

    /**
     * @return array<string, int>
     */
    private function listStats(string $viewType): array
    {
        $all = fn () => $viewType === 'intrapreneur' ? Company::query() : Business::entrepreneur();

        return [
            'totalBusinesses' => $all()->count(),
            'approvedBusinesses' => $all()->where('approval_status', 'approved')->count(),
            'pendingBusinesses' => $all()->where('approval_status', 'pending')->count(),
            'rejectedBusinesses' => $all()->whereIn('approval_status', ['rejected', 'need_revision'])->count(),
            'featuredBusinessesCount' => $viewType === 'intrapreneur' ? 0 : $all()->where('is_featured', true)->count(),
        ];
    }

    // --- Helpers ---

    private function validatedStatus(Request $request): string
    {
        $this->validate($request, [
            'status' => 'required|in:approved,rejected,need_revision,pending',
            'rejection_reason' => 'required_if:status,rejected,need_revision',
        ]);

        return $request->input('status');
    }

    /**
     * JSON for the list's fetch calls, a redirect with a flash message otherwise.
     */
    private function reply(Request $request, string $message, bool $success = true): RedirectResponse|JsonResponse
    {
        if ($request->ajax() || $request->wantsJson()) {
            return new JsonResponse(['success' => $success, 'message' => $message], $success ? 200 : 422);
        }

        return $success ? back()->with('success', $message) : back()->withErrors(['approval' => $message]);
    }
}
