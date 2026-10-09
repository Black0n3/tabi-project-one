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
                canvas: {
                    DEFAULT: '#0A0A0A',
                    raised: '#121212',
                },
                ink: {
                    DEFAULT: '#F3F1EC',
                    soft: 'rgba(243,241,236,0.64)',
                    faint: 'rgba(243,241,236,0.38)',
                },
                line: {
                    DEFAULT: 'rgba(255,255,255,0.09)',
                    strong: 'rgba(255,255,255,0.20)',
                },
                panel: {
                    DEFAULT: 'rgba(255,255,255,0.045)',
                    strong: 'rgba(255,255,255,0.075)',
                },
            },
        },
    },

    plugins: [forms],
};
