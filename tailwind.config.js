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
                sans: ['Inter', 'Noto Sans Devanagari', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309', // Primary brand ochre / terracotta
                    800: '#92400e',
                    900: '#78350f',
                    950: '#451a03',
                },
                library: {
                    parchment: '#fdfbf7',
                    surface: '#ffffff',
                    muted: '#f8fafc',
                    border: '#e2e8f0',
                },
            },
            aspectRatio: {
                'book': '2 / 3',
            },
            boxShadow: {
                'book': '0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -2px rgba(0, 0, 0, 0.04)',
                'book-hover': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.05)',
            },
        },
    },

    plugins: [forms],
};
