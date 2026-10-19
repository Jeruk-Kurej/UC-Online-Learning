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
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
                display: ['Poppins', ...defaultTheme.fontFamily.sans],
            },

            // --- Type scale: nothing below 13px, body 16px (Poppins runs wide, so line heights stay generous) ---
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
                // --- UC orange (#ff8800 is brand-500, as on online.ciputra.ac.id). White text needs brand-700 or darker; text on brand-500 is ink ---
                brand: {
                    50: '#fff6ea',
                    100: '#ffe5cc',
                    200: '#ffd0a1',
                    300: '#ffb34f',
                    400: '#ff9a1f',
                    500: '#ff8800',
                    600: '#d97200',
                    700: '#a65300',
                    800: '#823f00',
                    900: '#2b1604',
                },
                // --- Dark grey of the UC footer and of text on orange ---
                ink: '#222222',
                // --- Page background and borders ---
                canvas: '#f6f6f6',
                line: '#dcdcdc',
            },
        },
    },

    plugins: [forms],
};
