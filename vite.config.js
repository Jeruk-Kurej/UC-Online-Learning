import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/pages/offering-form.css',
                'resources/css/pages/work-profile-create.css',
                'resources/css/pages/work-profile-edit.css',
                'resources/css/pages/user-form.css',
                'resources/css/pages/welcome.css',
                'resources/css/vendor/tom-select.css',
            ],
            refresh: true,
        }),
    ],
});
