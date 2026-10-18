<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ImportController extends Controller
{
    public function progress(string $importId): JsonResponse
    {
        $prefix = "import_{$importId}";

        $progress = Cache::get($prefix);

        if (! $progress) {
            $progress = [
                'status' => 'processing',
                'errors' => [],
            ];
        }

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

    public function checkActive(Request $request): JsonResponse
    {
        $session = $request->session();
        $importId = $session->get('active_import')
            ?: $session->get('importId')
            ?: $session->get('active_business_import_id')
            ?: $session->get('active_user_import_id');

        if ($importId) {
            return new JsonResponse(['importId' => $importId]);
        }

        return new JsonResponse(['importId' => null]);
    }

    public function clearActive(Request $request): JsonResponse
    {
        $request->session()->forget([
            'active_import',
            'importId',
            'active_business_import_id',
            'active_user_import_id',
        ]);

        return new JsonResponse(['status' => 'cleared']);
    }
}
