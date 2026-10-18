<?php

namespace App\View\Components\Featured;

use App\Models\User;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Featured student on the home page with their first company (intrapreneur) or first venture (entrepreneur).
 */
class StudentCard extends Component
{
    public bool $isIntra;

    public mixed $highlight;

    public function __construct(public User $student, public string $type = 'intra')
    {
        $this->isIntra = $type === 'intra';
        $this->highlight = $this->isIntra ? $student->companies->first() : $student->businesses->first();
    }

    public function render(): View
    {
        return view('components.featured.student-card');
    }
}
