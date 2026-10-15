<?php

namespace App\View\Components\Featured;

use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Home page carousel of featured students, intrapreneurs (blue) or entrepreneurs (orange).
 */
class StudentsSection extends Component
{
    // --- Decoration, colours and copy per kind ---

    private const KINDS = [
        'intra' => [
            'spacing' => 'pt-24 pb-12',
            'decor' => [
                'uco-ambient-glow uco-ambient-glow--blue uco-floating-blob-slow -top-20 -left-20',
                'uco-floating-shape uco-floating-shape--plus-blue top-[15%] right-[10%]',
                'uco-floating-shape uco-floating-shape--ring-blue bottom-[25%] left-[5%]',
                'uco-floating-shape uco-floating-shape--dot top-[50%] right-[25%]',
            ],
            'outlineTone' => 'uco-outline-bg-text--blue',
            'outlineText' => 'INTRA',
            'title' => 'uco-section-title--blue',
            'gradient' => 'uco-text-gradient-blue',
            'word' => 'Intrapreneur',
            'lead' => 'Meet outstanding intrapreneurs driving innovation within corporate ecosystems.',
            'tag' => 'Corporate Innovators',
            'emptyIcon' => 'bi-people text-2xl text-blue-400',
            'emptyNoun' => 'intrapreneur',
        ],
        'entre' => [
            'spacing' => 'py-12',
            'decor' => [
                'uco-ambient-glow uco-ambient-glow--orange uco-floating-blob-slow -top-10 -right-20 opacity-60',
                'uco-floating-shape uco-floating-shape--plus top-[20%] left-[8%]',
                'uco-floating-shape uco-floating-shape--ring bottom-[20%] right-[10%]',
            ],
            'outlineTone' => 'uco-outline-bg-text--orange',
            'outlineText' => 'ENTRE',
            'title' => '',
            'gradient' => 'uco-text-gradient-orange',
            'word' => 'Entrepreneur',
            'lead' => 'Discover student founders building ventures and startups across the UCO network.',
            'tag' => 'Student Founders',
            'emptyIcon' => 'bi-rocket-takeoff text-2xl text-uco-orange-400',
            'emptyNoun' => 'entrepreneur',
        ],
    ];

    public array $look;

    public function __construct(public Collection $students, public string $type = 'intra')
    {
        $this->look = self::KINDS[$type];
    }

    public function render(): View
    {
        return view('components.featured.students-section');
    }
}
