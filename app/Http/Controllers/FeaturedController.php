<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeaturedController extends Controller
{
    // --- Every featured item is equal: the order changes once a day, so nobody stays first, and each section shows at most this many ---

    private const SECTION_LIMIT = 6;

    public function index(Request $request): View
    {
        return view('featured.index', [
            'featuredVentures' => Business::visible()
                ->entrepreneur()
                ->where('is_featured', true)
                ->newestFirst()
                ->with(['category', 'user', 'members', 'products'])
                ->get()
                ->pipe(fn (Collection $items) => $this->inDailyOrder($items)),
            'topEntrepreneurs' => $this->featuredStudents('entrepreneur')
                ->with(['businesses' => fn ($q) => $q->visible()->entrepreneur()->with('category')])
                ->newestFirst()
                ->get()
                ->pipe(fn (Collection $items) => $this->inDailyOrder($items)),
            'topIntrapreneurs' => $this->featuredStudents('intrapreneur')
                ->with(['companies' => fn ($q) => $q->visible()->with('category')])
                ->newestFirst()
                ->get()
                ->pipe(fn (Collection $items) => $this->inDailyOrder($items)),
            'testimonies' => $this->inDailyOrder($this->communityTestimonies()),
        ]);
    }

    // --- Home page sections ---

    /**
     * The same order all day, a different one tomorrow (sorted by a hash of the day and the id, so it does not depend on the PHP version or the database), cut to the section limit.
     * md5, not crc32: crc32 is linear, so changing only the date shifts every hash the same way and the order barely moves from one day to the next.
     */
    private function inDailyOrder(Collection $items): Collection
    {
        $day = now()->format('Ymd');

        return $items->sortBy(fn ($item) => md5($day.'-'.$item->getKey()))->take(self::SECTION_LIMIT)->values();
    }

    /**
     * Admin-starred, visible students with a photo whose career status is $status.
     */
    private function featuredStudents(string $status): Builder
    {
        return User::query()
            ->where('is_visible', true)
            ->where('is_featured', true)
            ->where('role', '!=', 'admin')
            ->whereNotNull('profile_photo_url')
            ->whereRaw('LOWER(current_status) = ?', [$status]);
    }

    /**
     * Testimonies picked for the home page (not tied to is_featured): AI-approved ones (score >= 80) and any
     * visible testimony that was never scored.
     */
    private function communityTestimonies(): Collection
    {
        return User::where('is_visible', true)
            ->where('is_featured_testimony', true)
            ->withTestimony()
            ->whereNotNull('profile_photo_url')
            ->where(function ($q) {
                $q->where('ai_score', '>=', 80)
                    ->orWhere(function ($q2) {
                        $q2->whereNull('ai_score')->where('is_visible', true);
                    });
            })
            ->newestFirst()
            ->get();
    }
}
