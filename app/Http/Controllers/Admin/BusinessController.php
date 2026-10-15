<?php

namespace App\Http\Controllers\Admin;

use App\Models\Business;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Admin moderation of businesses and work profiles: lists, approval, featuring, deletion and import.
 */
class BusinessController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of businesses for administrative management.
     */
    public function index(Request $request): View|Response
    {
        $viewType = $request->get('type', 'entrepreneur');
        $status = $request->get('status');
        $search = $request->get('search');
        $featured = $request->get('featured');

        if ($viewType === 'intrapreneur') {
            // --- Intrapreneur (Company) mode ---
            $query = Company::with(['user', 'category']);

            $totalBusinesses = Company::count();
            $approvedBusinesses = Company::where('approval_status', 'approved')->count();
            $pendingBusinesses = Company::where('approval_status', 'pending')->count();
            $rejectedBusinesses = Company::whereIn('approval_status', ['rejected', 'need_revision'])->count();
            $featuredBusinessesCount = 0;

            if ($status) {
                $query->where('approval_status', $status);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('position', 'LIKE', "%{$search}%")
                      ->orWhereHas('user', function ($uq) use ($search) {
                          $uq->where('name', 'LIKE', "%{$search}%");
                      });
                });
            }
        } else {
            // --- Entrepreneur (Business) mode ---
            $viewType = 'entrepreneur';
            $query = Business::with(['user', 'category'])->entrepreneur();

            $totalBusinesses = Business::entrepreneur()->count();
            $pendingBusinesses = Business::entrepreneur()->where('approval_status', 'pending')->count();
            $approvedBusinesses = Business::entrepreneur()->where('approval_status', 'approved')->count();
            $rejectedBusinesses = Business::entrepreneur()->whereIn('approval_status', ['rejected', 'need_revision'])->count();
            $featuredBusinessesCount = Business::entrepreneur()->where('is_featured', true)->count();

            if ($status) {
                $query->where('approval_status', $status);
            }

            if ($featured === 'yes') {
                $query->where('is_featured', true);
            } elseif ($featured === 'no') {
                $query->where('is_featured', false);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhereHas('user', function ($uq) use ($search) {
                          $uq->where('name', 'LIKE', "%{$search}%");
                      });
                });
            }
        }

        $businesses = $query->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()
                ->view('businesses.admin.partials.list', compact('businesses', 'viewType'))
                ->header('Vary', 'X-Requested-With');
        }

        return view('businesses.admin.index', compact(
            'businesses',
            'viewType',
            'status',
            'search',
            'featured',
            'totalBusinesses',
            'pendingBusinesses',
            'approvedBusinesses',
            'rejectedBusinesses',
            'featuredBusinessesCount'
        ));
    }

    /**
     * Update business status (admin only).
     */
    public function updateStatus(Request $request, Business $business): RedirectResponse
    {
        $status = $this->validatedStatus($request);
        $business->applyApprovalStatus($status, $request->input('rejection_reason'));

        return back()->with('success', 'Business status updated to '.ucfirst(str_replace('_', ' ', (string) $status)));
    }

    /**
     * Update company status (admin only).
     */
    public function updateCompanyStatus(Request $request, Company $company): RedirectResponse
    {
        $status = $this->validatedStatus($request);
        $company->applyApprovalStatus($status, $request->input('rejection_reason'));

        return back()->with('success', 'Company status updated to '.ucfirst(str_replace('_', ' ', (string) $status)));
    }

    /**
     * Approve a business (admin only).
     */
    public function approve(Business $business): RedirectResponse
    {
        $business->is_visible = true;
        $business->save();

        return back()->with('success', "Business \"{$business->name}\" has been approved and is now visible.");
    }

    /**
     * Toggle the featured status of a business.
     */
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

    /**
     * Delete a business.
     */
    public function destroy(Business $business): RedirectResponse
    {
        $business->delete();

        return redirect()->route('businesses.index')
            ->with('success', 'Business deleted successfully.');
    }

    /**
     * Import businesses from CSV/Excel using auto-detected importer.
     */
    public function import(Request $request): RedirectResponse|JsonResponse
    {
        $this->validate($request, [
            'file' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        try {
            $importId = (string) Str::uuid();
            /** @var \Illuminate\Http\UploadedFile $file */
            $file = $request->file('file');

            $path = $file->store('imports', 'local');

            $importer = \App\Factories\ImporterFactory::make($file->getRealPath(), $importId, $file->getClientOriginalName());

            Excel::queueImport($importer, $path, 'local');

            $format = $importer instanceof \App\Imports\UCOStudentImport ? 'UCO Student Profile' : 'Form Response';

            session(['active_import' => $importId]);

            if ($request->wantsJson()) {
                return new JsonResponse([
                    'success' => true,
                    'importId' => $importId,
                    'format' => $format,
                    'message' => "Import queued! Format: {$format}.",
                ]);
            }

            return back()->with('success', "Import queued! Format: {$format}. Processing ~1500 rows in background...")
                ->with('importId', $importId);
        } catch (\Exception $e) {
            Log::error('Import error: '.$e->getMessage());
            if ($request->wantsJson()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Import failed: '.$e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['error' => $e->getMessage()]);
        }
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
