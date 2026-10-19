<?php

// --- Text colours must stay readable: WCAG AA asks for 4.5:1 on normal text ---

function tailwindColors(): array
{
    $config = file_get_contents(base_path('tailwind.config.js'));
    $groups = [];
    foreach (['brand'] as $group) {
        $start = strpos($config, $group.': {');
        $end = strpos($config, '}', $start);
        preg_match_all("/(\d{2,3}): '(#[0-9a-f]{6})'/i", substr($config, $start, $end - $start), $found, PREG_SET_ORDER);
        foreach ($found as [, $shade, $hex]) {
            $groups["$group-$shade"] = $hex;
        }
    }

    preg_match("/ink: '(#[0-9a-f]{6})'/i", $config, $ink);
    preg_match("/canvas: '(#[0-9a-f]{6})'/i", $config, $canvas);

    return $groups + [
        'ink' => $ink[1],
        'canvas' => $canvas[1],
        'white' => '#ffffff',
        'gray-100' => '#f3f4f6',
        'gray-200' => '#e5e7eb',
        'gray-300' => '#d1d5db',
        'gray-600' => '#4b5563',
        'gray-700' => '#374151',
        'gray-800' => '#1f2937',
        'gray-900' => '#111827',
        'red-100' => '#fee2e2',
        'red-700' => '#b91c1c',
        'red-800' => '#991b1b',
        'red-900' => '#7f1d1d',
        'green-100' => '#dcfce7',
        'green-800' => '#166534',
        'amber-100' => '#fef3c7',
        'amber-900' => '#78350f',
    ];
}

function luminance(string $hex): float
{
    $channels = array_map(function (string $part) {
        $value = hexdec($part) / 255;

        return $value <= 0.03928 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
    }, str_split(ltrim($hex, '#'), 2));

    return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
}

function contrast(string $foreground, string $background): float
{
    [$light, $dark] = [max(luminance($foreground), luminance($background)), min(luminance($foreground), luminance($background))];

    return ($light + 0.05) / ($dark + 0.05);
}

dataset('text pairs', [
    'button text' => ['white', 'brand-700'],
    'button text on hover' => ['white', 'brand-800'],
    'danger button' => ['white', 'red-700'],
    'outline button' => ['brand-800', 'white'],
    'outline danger button' => ['red-800', 'white'],
    'title on page' => ['brand-900', 'canvas'],
    'body text on page' => ['gray-800', 'canvas'],
    'muted text on page' => ['gray-600', 'canvas'],
    'muted text on card' => ['gray-600', 'white'],
    'hero search button' => ['ink', 'brand-500'],
    'call to action on orange' => ['ink', 'brand-500'],
    'button on orange' => ['white', 'ink'],
    'menu text' => ['gray-700', 'white'],
    'active menu text' => ['brand-900', 'white'],
    'orange accent text on white' => ['brand-700', 'white'],
    'link text on page' => ['brand-700', 'canvas'],
    'footer text' => ['gray-300', 'ink'],
    'footer headings' => ['brand-300', 'ink'],
    'active menu item' => ['brand-900', 'brand-50'],
    'info badge' => ['brand-800', 'brand-50'],
    'neutral badge' => ['gray-700', 'gray-100'],
    'success badge' => ['green-800', 'green-100'],
    'warning badge' => ['amber-900', 'amber-100'],
    'danger badge' => ['red-800', 'red-100'],
    'error text' => ['red-700', 'white'],
]);

it('keeps :dataset readable', function (string $foreground, string $background) {
    $colors = tailwindColors();

    expect(contrast($colors[$foreground], $colors[$background]))->toBeGreaterThanOrEqual(4.5);
})->with('text pairs');
