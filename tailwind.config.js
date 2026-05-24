import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // UCO Orange Palette (Primary Accent)
                'uco-orange': {
                    50: '#fff8f0',
                    100: '#ffeedd',
                    200: '#ffd9b3',
                    300: '#ffc088',
                    400: '#ffa85c',
                    500: '#ff8c2e',  // Main Orange Accent
                    600: '#f7931e',
                    700: '#e67300',
                    800: '#b85c00',
                    900: '#8a4500',
                },
                // UCO Yellow Palette (Secondary Accent)
                'uco-yellow': {
                    50: '#fffef0',
                    100: '#fffbd6',
                    200: '#fff6ad',
                    300: '#fff085',
                    400: '#ffe95c',
                    500: '#ffd633',  // Main Yellow Accent
                    600: '#fdb913',
                    700: '#d99a00',
                    800: '#b37d00',
                    900: '#8c6100',
                },
                // Professional Base Colors
                'soft-white': '#fafafa',
                'soft-gray': {
                    50: '#f9fafb',
                    100: '#f3f4f6',
                    200: '#e5e7eb',
                    300: '#d1d5db',
                    400: '#9ca3af',
                    500: '#6b7280',
                    600: '#4b5563',
                    700: '#374151',
                    800: '#1f2937',
                    900: '#111827',
                },
            },
            backgroundImage: {
                // Gradient presets untuk UCO
                'uco-gradient': 'linear-gradient(135deg, #f7931e 0%, #fdb913 100%)',
                'uco-gradient-vertical': 'linear-gradient(to bottom, #f7931e 0%, #fdb913 100%)',
                'uco-gradient-dark': 'linear-gradient(135deg, #ea580c 0%, #ca8a04 100%)',
            },
        },
    },

    plugins: [forms],
};
