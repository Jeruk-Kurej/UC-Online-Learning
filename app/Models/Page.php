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
        'hero' => ['label' => 'Hero Header', 'icon' => 'bi-layout-text-window-reverse'],
        'feature_cards' => ['label' => 'Feature Cards Grid', 'icon' => 'bi-grid-3x3-gap'],
        'stats_grid' => ['label' => 'Impact Stats Grid', 'icon' => 'bi-bar-chart-line'],
        'text_block' => ['label' => 'Text / FAQ Block', 'icon' => 'bi-chat-square-text'],
        'cta_banner' => ['label' => 'CTA Banner', 'icon' => 'bi-megaphone'],
    ];

    // --- Icons a feature card can pick ---

    public const FEATURE_ICONS = [
        'bi-rocket-takeoff' => 'Rocket (Rapid Launch)',
        'bi-people' => 'People (Global Network)',
        'bi-graph-up-arrow' => 'Graph (Scalable Growth)',
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
        return [
            [
                'type' => 'hero',
                'badge' => $content['hero']['badge'] ?? 'About UC Online Learning',
                'title' => $content['hero']['title'] ?? 'Building the Future of Student & Alumni Entrepreneurship',
                'subtitle' => $content['hero']['subtitle'] ?? 'Connecting founders, intrapreneurs, and corporate innovators across Universitas Ciputra.',
            ],
            [
                'type' => 'feature_cards',
                'badge' => $content['pillars']['badge'] ?? 'Pillars of Excellence',
                'title' => $content['pillars']['title'] ?? 'Built for Sustainable Impact',
                'subtitle' => $content['pillars']['subtitle'] ?? 'Designed to support founders and intrapreneurs at every phase of their growth journey.',
                'cards' => $content['pillars']['cards'] ?? [
                    ['title' => 'Rapid Launch', 'description' => 'We provide the tools and network needed to transform academic theories into viable market products within weeks, not years.', 'icon' => 'bi-rocket-takeoff'],
                    ['title' => 'Global Network', 'description' => 'Connect with a diverse community of alumni mentors, industry experts, and fellow entrepreneurs across all major industries.', 'icon' => 'bi-people'],
                    ['title' => 'Scalable Growth', 'description' => 'From local startups to multinational enterprises, our platform supports scaling businesses at every stage of their lifecycle.', 'icon' => 'bi-graph-up-arrow'],
                ],
            ],
            [
                'type' => 'stats_grid',
                'title' => $content['stats']['title'] ?? 'Driving Community Impact',
                'items' => $content['stats']['items'] ?? [
                    ['number' => '500+', 'label' => 'Active Ventures'],
                    ['number' => '1200+', 'label' => 'Graduated Founders'],
                    ['number' => '24', 'label' => 'Industry Categories'],
                    ['number' => '15+', 'label' => 'Years of Heritage'],
                ],
            ],
            [
                'type' => 'cta_banner',
                'heading' => $content['cta']['heading'] ?? 'Ready to build your legacy?',
                'subtitle' => $content['cta']['subtitle'] ?? 'Join the UCO community today and gain access to a world of entrepreneurial opportunities.',
                'primary_btn_text' => $content['cta']['primary_btn_text'] ?? 'Get Started Now',
                'secondary_btn_text' => $content['cta']['secondary_btn_text'] ?? 'Explore Directory',
            ],
        ];
    }
}
