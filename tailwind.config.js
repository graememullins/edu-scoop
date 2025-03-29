import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php', // Your application's Blade files
        './vendor/filament/**/*.blade.php', // Filament Blade templates
        './resources/**/*.js', // Optional if you're using JS files
        './resources/**/*.vue', // Optional if you're using Vue.js
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#ec4899', // Light mode color (Pink)
                    dark: '#9333ea',    // Dark mode color (Purple)
                },
                gray: {
                    DEFAULT: '#64748b', // Light mode gray (Slate)
                    dark: '#1e293b',    // Dark mode gray (Dark Slate)
                },
            },
        },
    },

    darkMode: 'class', // Enable dark mode with 'class' strategy

    plugins: [
        forms, // Tailwind CSS Forms plugin
    ],
};