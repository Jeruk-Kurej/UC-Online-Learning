<?php

namespace App\View\Components\Offerings;

use App\Models\Business;
use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Create / edit form for a product or a service (one table, told apart by `type`).
 */
class Form extends Component
{
    // --- Look and copy per type (the two pages were styled separately) ---

    private const TYPES = [
        'product' => [
            'header' => 'mb-8 flex items-center gap-4',
            'back' => 'btn-uco btn-uco-secondary mb-4 sm:mb-0',
            'title' => 'text-3xl font-bold text-soft-gray-900 tracking-tight',
            'subtitle' => 'text-sm text-soft-gray-600 mt-1',
            'preview' => 'absolute inset-0 w-full h-full flex items-center justify-center',
            'actions' => 'flex items-center justify-between pt-6 border-t-2 border-soft-gray-100',
            'namePlaceholder' => 'e.g., Nasi Goreng Spesial',
            'descriptionPlaceholder' => 'Describe your product...',
            'pricePlaceholder' => '15000',
        ],
        'service' => [
            'header' => 'mb-6 flex flex-col sm:flex-row sm:items-center gap-4',
            'back' => 'btn-uco btn-uco-secondary',
            'title' => 'text-2xl font-bold text-gray-900',
            'subtitle' => 'text-sm text-gray-600',
            'preview' => 'w-full h-full relative overflow-hidden flex items-center justify-center',
            'actions' => 'flex items-center justify-between pt-6 border-t border-gray-200',
            'namePlaceholder' => 'e.g., Web Development, Haircut, Delivery Service',
            'descriptionPlaceholder' => 'Describe your service in detail...',
            'pricePlaceholder' => '50000',
        ],
    ];

    public array $look;

    public string $noun;

    public bool $isEdit;

    public ?string $selectedPriceType;

    public function __construct(
        public Business $business,
        public string $type,
        public ?Product $offering = null,
        public bool $photoRequired = false,
    ) {
        $this->look = self::TYPES[$type];
        $this->noun = ucfirst($type);
        $this->isEdit = $offering !== null;
        $this->selectedPriceType = $this->isEdit ? $this->editPriceType($offering) : old('price_type');
    }

    // --- An offering saved without a price shows as "Unspecified" unless it was set to a priceless type ---

    private function editPriceType(Product $offering): ?string
    {
        $selected = old('price_type', $offering->price_type);
        if (($offering->price === null || trim($offering->price) === '') && ! in_array($selected, ['unspecified', 'customize'])) {
            return 'unspecified';
        }

        return $selected;
    }

    public function render(): View
    {
        return view('components.offerings.form');
    }
}
