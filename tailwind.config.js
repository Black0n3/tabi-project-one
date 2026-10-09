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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                display: ['Fraunces', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                navy: {
                    950: '#06122B',
                    900: '#0A1E45',
                    800: '#0E2A5F',
                    700: '#15367A',
                },
                brand: {
                    DEFAULT: '#0F3F9E',
                    dark: '#0B3380',
                    light: '#E9EFFB',
                    soft: '#D6E2F8',
                    sky: '#7FB0FF',
                },
                ink: {
                    DEFAULT: '#0B1B3A',
                    soft: '#4A5873',
                    faint: '#7B879E',
                },
                line: {
                    DEFAULT: '#E3E8F0',
                    strong: '#CBD5E3',
                },
                mist: '#F5F7FB',
            },
            boxShadow: {
                card: '0 1px 2px rgba(10,30,69,.04), 0 8px 24px -12px rgba(10,30,69,.12)',
                lift: '0 18px 40px -18px rgba(10,30,69,.28)',
            },
        },
    },

    plugins: [forms],
};
