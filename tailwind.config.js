import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Core backgrounds - Dungeon Depths
                graphite: {
                    DEFAULT: '#2A2B2E',
                    50: '#3A3B3F',
                    100: '#2A2B2E',
                    900: '#1A1B1E',
                },
                gunmetal: {
                    DEFAULT: '#42434A',
                    light: '#52535A',
                    dark: '#32333A',
                },
                charcoal: {
                    DEFAULT: '#5A5A66',
                    light: '#6A6A76',
                    dark: '#4A4A56',
                },
                // Arcane accent colors - Mystic Arcanist
                arcane: {
                    purple: '#806EB3',
                    periwinkle: '#A682FF',
                    grey: '#C4C1E0',      // Lightened for better contrast
                    lavender: '#A5A2D4', // Original for accents only
                },
                // Text colors - High contrast
                slate: {
                    200: '#E2E8F0',  // Primary body text
                    300: '#CBD5E1',  // Secondary text
                    400: '#94A3B8',  // Muted text
                    500: '#64748B',  // Placeholder text
                },
                // Nature/Success colors
                nature: {
                    DEFAULT: '#A4C2A8',
                    dark: '#8AAF8E',
                    light: '#B8D4BC',
                },
                // Legendary/Gold colors
                legendary: {
                    gold: '#D2BB5C',
                    amber: '#FFB30F',
                },
                // Danger colors
                danger: {
                    DEFAULT: '#EF4444',
                    dark: '#DC2626',
                    light: '#F87171',
                },
            },
            backgroundImage: {
                // Arcane Flow gradient - primary buttons
                'arcane-flow': 'linear-gradient(135deg, #806EB3 0%, #A682FF 100%)',
                'arcane-flow-hover': 'linear-gradient(135deg, #9080C3 0%, #B692FF 100%)',
                // Luminous border gradients
                'luminous-border': 'linear-gradient(135deg, #A682FF 0%, #5A5A66 100%)',
                'luminous-border-subtle': 'linear-gradient(135deg, rgba(166, 130, 255, 0.5) 0%, rgba(90, 90, 102, 0.5) 100%)',
                // Nature gradient
                'nature-flow': 'linear-gradient(135deg, #8AAF8E 0%, #A4C2A8 100%)',
                // Legendary gradient
                'legendary-flow': 'linear-gradient(135deg, #D2BB5C 0%, #FFB30F 100%)',
                // Ethereal mist for modals/special cards
                'ethereal-mist': 'linear-gradient(135deg, #A682FF 0%, #A5A2D4 100%)',
            },
            boxShadow: {
                // Glow shadows - Arcane
                'glow-arcane': '0 0 20px rgba(166, 130, 255, 0.3)',
                'glow-arcane-sm': '0 0 10px rgba(166, 130, 255, 0.2)',
                'glow-arcane-lg': '0 0 30px rgba(166, 130, 255, 0.4)',
                // Glow shadows - Nature
                'glow-nature': '0 0 20px rgba(164, 194, 168, 0.3)',
                'glow-nature-sm': '0 0 10px rgba(164, 194, 168, 0.2)',
                // Glow shadows - Legendary
                'glow-legendary': '0 0 20px rgba(255, 179, 15, 0.3)',
                'glow-legendary-sm': '0 0 10px rgba(255, 179, 15, 0.2)',
                // Glow shadows - Danger
                'glow-danger': '0 0 20px rgba(239, 68, 68, 0.3)',
                // Dark elevation shadows
                'dark-sm': '0 1px 2px rgba(0, 0, 0, 0.3)',
                'dark-md': '0 4px 6px rgba(0, 0, 0, 0.4)',
                'dark-lg': '0 10px 15px rgba(0, 0, 0, 0.5)',
                'dark-xl': '0 20px 25px rgba(0, 0, 0, 0.6)',
            },
            backdropBlur: {
                xs: '2px',
            },
            animation: {
                'glow-pulse': 'glow-pulse 2s ease-in-out infinite',
                'shimmer': 'shimmer 2s linear infinite',
            },
            keyframes: {
                'glow-pulse': {
                    '0%, 100%': { boxShadow: '0 0 20px rgba(166, 130, 255, 0.3)' },
                    '50%': { boxShadow: '0 0 30px rgba(166, 130, 255, 0.5)' },
                },
                'shimmer': {
                    '0%': { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' },
                },
            },
        },
    },

    plugins: [forms],
};
