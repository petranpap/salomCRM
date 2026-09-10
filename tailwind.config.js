import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                // Legacy warm palette (existing views)
                'rose-sand':    { DEFAULT: '#C9907A', light: '#F3E3DC', hover: '#A5705C', dark: '#E0A090' },
                'cream':        { DEFAULT: '#FAF7F4', soft: '#F2EDE8', dark: '#1C1210', 'dark-soft': '#251917' },
                'espresso':     { DEFAULT: '#2C1F1A', muted: '#8C7168', subtle: '#D4A59A' },
                'card-dark':    '#2E1E1A',
                'border-warm':  { DEFAULT: '#E8DDD8', soft: '#F0E9E5', dark: '#3D2820' },

                // Terra & Silk design system (dashboard, sidebar, new views)
                'ts': {
                    'bg':             '#fcf9f6',
                    'surface':        '#ffffff',
                    'surface-low':    '#f6f3f0',
                    'surface-mid':    '#f0edea',
                    'surface-high':   '#eae8e5',
                    'primary':        '#7d523c',
                    'primary-dim':    '#986a52',
                    'primary-light':  '#ffdbcb',
                    'on-primary':     '#ffffff',
                    'secondary':      '#645d53',
                    'secondary-bg':   '#e8ded1',
                    'text':           '#1b1c1a',
                    'text-muted':     '#51443e',
                    'text-subtle':    '#83746d',
                    'border':         '#d5c3bb',
                    'border-soft':    '#e5e2df',
                    'error':          '#ba1a1a',
                    'error-light':    '#ffdad6',
                    'warning':        '#b45309',
                    'warning-light':  '#fef3c7',
                    'success':        '#166534',
                    'success-light':  '#dcfce7',
                },
            },
            fontFamily: {
                display: ['DM Serif Display', 'Playfair Display', ...defaultTheme.fontFamily.serif],
                sans:    ['DM Sans', ...defaultTheme.fontFamily.sans],
                mono:    ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            borderRadius: {
                'card': '14px',
                'card-lg': '16px',
                'pill': '9999px',
            },
            boxShadow: {
                'silk':    '0 1px 3px rgba(125,82,60,0.06), 0 4px 16px rgba(125,82,60,0.04)',
                'silk-md': '0 2px 8px rgba(125,82,60,0.08), 0 8px 24px rgba(125,82,60,0.06)',
                'silk-lg': '0 4px 16px rgba(125,82,60,0.10), 0 16px 48px rgba(125,82,60,0.08)',
            },
            transitionDuration: {
                DEFAULT: '200ms',
            },
        },
    },
    plugins: [forms],
};
