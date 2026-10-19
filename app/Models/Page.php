<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['slug', 'title', 'content_json'];

    protected $casts = [
        'content_json' => 'array',
    ];

    // --- Section types the About builder can add (button label and icon) ---

    public const SECTION_TYPES = [
        'hero' => ['label' => 'Page title', 'icon' => 'bi-layout-text-window-reverse'],
        'feature_cards' => ['label' => 'Cards', 'icon' => 'bi-grid-3x3-gap'],
        'stats_grid' => ['label' => 'Numbers', 'icon' => 'bi-bar-chart-line'],
        'text_block' => ['label' => 'Text', 'icon' => 'bi-chat-square-text'],
        'cta_banner' => ['label' => 'Call to action', 'icon' => 'bi-megaphone'],
    ];

    // --- Icons a feature card can pick ---

    public const FEATURE_ICONS = [
        'bi-search' => 'Magnifier',
        'bi-rocket-takeoff' => 'Rocket',
        'bi-people' => 'People',
        'bi-graph-up-arrow' => 'Graph',
        'bi-lightning-charge' => 'Lightning',
        'bi-shield-check' => 'Shield',
        'bi-trophy' => 'Trophy',
        'bi-star' => 'Star',
        'bi-lightbulb' => 'Lightbulb',
    ];

    // --- Sections ---

    /**
     * Saved sections, or the default About layout filled from older hero/pillars/stats/cta keys.
     */
    public static function sectionsFor(?self $page): array
    {
        $content = $page?->content_json ?? [];
        if (is_string($content)) {
            $content = json_decode($content, true) ?: [];
        }

        $sections = $content['sections'] ?? [];

        return empty($sections) ? self::defaultSections($content) : $sections;
    }

    private static function defaultSections(array $content): array
    {
        $sections = [
            [
                'type' => 'hero',
                'badge' => $content['hero']['badge'] ?? '',
                'title' => $content['hero']['title'] ?? 'About UC Online Learning',
                'subtitle' => $content['hero']['subtitle'] ?? 'A directory of the businesses and careers of Universitas Ciputra Online Learning students and alumni.',
            ],
            [
                'type' => 'feature_cards',
                'badge' => $content['pillars']['badge'] ?? '',
                'title' => $content['pillars']['title'] ?? 'What you can do here',
                'subtitle' => $content['pillars']['subtitle'] ?? '',
                'cards' => $content['pillars']['cards'] ?? [
                    ['title' => 'Find businesses and people', 'description' => 'Browse the directory to see what students and alumni have built and where they work.', 'icon' => 'bi-search'],
                    ['title' => 'Show your work', 'description' => 'Add your business, products and services or your work profile. Once the admin team approves them, they appear in the directory.', 'icon' => 'bi-rocket-takeoff'],
                    ['title' => 'Connect', 'description' => 'Send a collab request to someone you would like to work with. They receive it in their inbox.', 'icon' => 'bi-people'],
                ],
            ],
        ];

        if (isset($content['stats'])) {
            $sections[] = [
                'type' => 'stats_grid',
                'title' => $content['stats']['title'] ?? '',
                'items' => $content['stats']['items'] ?? [],
            ];
        }

        if (isset($content['cta'])) {
            $sections[] = [
                'type' => 'cta_banner',
                'heading' => $content['cta']['heading'] ?? '',
                'subtitle' => $content['cta']['subtitle'] ?? '',
                'primary_btn_text' => $content['cta']['primary_btn_text'] ?? '',
                'secondary_btn_text' => $content['cta']['secondary_btn_text'] ?? '',
            ];
        }

        return $sections;
    }
}
