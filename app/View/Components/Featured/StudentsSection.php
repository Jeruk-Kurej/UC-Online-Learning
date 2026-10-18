<?php

namespace App\View\Components\Featured;

use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Home page list of featured students, entrepreneurs or intrapreneurs.
 */
class StudentsSection extends Component
{
    // --- Copy per kind ---

    private const KINDS = [
        'intra' => [
            'title' => 'Featured intrapreneurs',
            'lead' => 'Students and alumni building their careers inside companies.',
            'view' => 'intrapreneur',
            'emptyTitle' => 'No featured intrapreneurs yet',
        ],
        'entre' => [
            'title' => 'Featured entrepreneurs',
            'lead' => 'Students and alumni who run their own ventures.',
            'view' => 'entrepreneur',
            'emptyTitle' => 'No featured entrepreneurs yet',
        ],
    ];

    /**
     * @var array<string, string>
     */
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
