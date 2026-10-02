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
                ink: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                    950: '#0b1120',
                },
                warm: {
                    50: '#fcfbf7', // Warm ivory background
                    100: '#f7f5f0',
                    200: '#eeebe2',
                    300: '#e2ded5',
                    400: '#d1ccc0',
                    500: '#b5ada0',
                    800: '#3b3730',
                    900: '#26231e',
                },
                terracotta: {
                    50: '#fdf6f2',
                    100: '#f9ece5',
                    200: '#f3d6c8',
                    300: '#ebb8a3',
                    500: '#c25e38', // Muted terracotta accent
                    600: '#ad4e29',
                    700: '#8c3d1f',
                    800: '#70321c',
                    900: '#5c2b1a',
                },
                sage: {
                    50: '#f4f7f5',
                    100: '#e6ede8',
                    200: '#cedcd1',
                    500: '#52796f', // Muted sage secondary
                    600: '#406359',
                    700: '#314c44',
                    800: '#283c37',
                    900: '#22322e',
                },
                charcoal: {
                    DEFAULT: '#1e293b',
                    muted: '#475569',
                    subtle: '#64748b',
                },
                library: {
                    parchment: '#f3f8fc',
                    surface: '#ffffff',
                    muted: '#f0f4f8',
                    border: '#e2e8f0',
                },
                booknest: {
                    bg: '#f3f8fc',
                    card: '#ffffff',
                    cyan: '#48b3e8',
                    'cyan-hover': '#32a2db',
                    'cyan-light': '#e8f5fd',
                    'cyan-glow': '#bfe3f7',
                },
                cocoa: {
                    950: '#1c1511',
                    900: '#2c2018',
                    800: '#453326',
                    700: '#604735',
                    600: '#7d5d47',
                    sand: '#d6c3b2',
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
