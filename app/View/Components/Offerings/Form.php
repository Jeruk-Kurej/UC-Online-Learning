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
    // --- Example texts per type ---

    private const EXAMPLES = [
        'product' => ['name' => 'For example Nasi Goreng Spesial', 'description' => 'Describe your product.', 'price' => '15000'],
        'service' => ['name' => 'For example Web development or haircut', 'description' => 'Describe your service in detail.', 'price' => '50000'],
    ];

    public array $example;

    public string $noun;

    public bool $isEdit;

    public ?string $selectedPriceType;

    public function __construct(
        public Business $business,
        public string $type,
        public ?Product $offering = null,
        public bool $photoRequired = false,
    ) {
        $this->example = self::EXAMPLES[$type];
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
