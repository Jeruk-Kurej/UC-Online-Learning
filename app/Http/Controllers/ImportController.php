<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Class ImportController
 *
 * Tracks background import process statistics (such as total, processed, successful, skipped rows)
 * and active session states during CSV/Excel file loading.
 */
class ImportController extends Controller
{
    /**
     * Track import progress by session ID.
     *
     * @param  string  $importId  Unique import identifier
     */
    public function progress(string $importId): JsonResponse
    {
        $prefix = "import_{$importId}";

        // Fetch status and errors from the shared cache (Database)
        $progress = Cache::get($prefix);

        if (! $progress) {
            // Fallback to ensure UI has something to work with initially
            $progress = [
                'status' => 'processing',
                'errors' => [],
            ];
        }

        // Fetch atomic counts pushed by the background worker
        $total = (int) Cache::get("{$prefix}_total", 0);
        $current = (int) Cache::get("{$prefix}_current", 0);
        $success = (int) Cache::get("{$prefix}_success", 0);
        $skipped = (int) Cache::get("{$prefix}_skipped", 0);

        $progress['total'] = $total;
        $progress['current'] = $current;
        $progress['success'] = $success;
        $progress['skipped'] = $skipped;

        return new JsonResponse($progress);
    }

    /**
     * Check if there's an active import in the current session and return its ID.
     */
    public function checkActive(Request $request): JsonResponse
    {
        $session = $request->session();
        // Check all possible session keys where import IDs might be stored
        $importId = $session->get('active_import')
            ?: $session->get('importId')
            ?: $session->get('active_business_import_id')
            ?: $session->get('active_user_import_id');

        if ($importId) {
            return new JsonResponse(['importId' => $importId]);
        }

        return new JsonResponse(['importId' => null]);
    }

    /**
     * Clear the active import session after completion or manual dismiss.
     */
    public function clearActive(Request $request): JsonResponse
    {
        // Clear all possible session keys used for imports
        $request->session()->forget([
            'active_import',
            'importId',
            'active_business_import_id',
            'active_user_import_id',
        ]);

        return new JsonResponse(['status' => 'cleared']);
    }
}
