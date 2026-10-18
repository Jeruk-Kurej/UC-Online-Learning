import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        // Scripts moved out of Blade.
        './resources/js/{pages,components,lib}/**/*.js',
        // Class components (in their group folders) that pick Tailwind classes in PHP.
        './app/View/Components/*/*.php',
    ],

    // Tom Select builds its markup in JavaScript, so its class names never appear in the views.
    // Keep the component rules styled for them (resources/css/components/forms.css).
    safelist: [{ pattern: /^ts-/ }],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Source Sans 3"', ...defaultTheme.fontFamily.sans],
                serif: ['"Source Serif 4"', ...defaultTheme.fontFamily.serif],
            },

            // --- Type scale: nothing below 13px, body 16px ---
            fontSize: {
                xs: ['0.8125rem', '1.125rem'],
                sm: ['0.9375rem', '1.375rem'],
                base: ['1rem', '1.625rem'],
                lg: ['1.125rem', '1.75rem'],
                xl: ['1.25rem', '1.75rem'],
                '2xl': ['1.5rem', '2rem'],
                '3xl': ['1.875rem', '2.375rem'],
                '4xl': ['2.25rem', '2.75rem'],
                '5xl': ['3rem', '3.5rem'],
            },

            colors: {
                // --- UC navy from the logo (#082848 is brand-900); links, buttons, header details ---
                brand: {
                    50: '#eef3f9',
                    100: '#dbe6f2',
                    200: '#b9cde4',
                    300: '#8fadd0',
                    400: '#5d85b5',
                    500: '#2f5f98',
                    600: '#174a80',
                    700: '#0d3a6b',
                    800: '#0a2f57',
                    900: '#082848',
                },
                // --- UC gold from the logo: rules and small marks; use gold-700 for text on white ---
                gold: {
                    100: '#f6f1e2',
                    300: '#d8caa0',
                    500: '#a89868',
                    700: '#7a6a2e',
                },
                // --- Page background and borders ---
                canvas: '#f4f6f9',
                line: '#d5dce5',
            },
        },
    },

    plugins: [forms],
};
