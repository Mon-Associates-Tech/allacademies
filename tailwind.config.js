const defaultTheme = require('tailwindcss/defaultTheme');
const colors       = require('tailwindcss/colors');

// Tailwind CSS v3 renamed five legacy color aliases and kept them purely for
// backward-compat. Spreading the full `colors` object includes them and triggers
// a deprecation warning for each. Destructure them out so only the canonical
// v3 names are merged into the palette.
const {
    lightBlue, // → sky
    warmGray,  // → stone
    trueGray,  // → neutral
    coolGray,  // → gray
    blueGray,  // → slate
    ...safeColors
} = colors;

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        // Single-file entry for a legacy class-based Livewire v2 component that
        // lives outside the standard resources/ tree.
        './app/Http/Livewire/ExaminationHeading.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            colors: {
                ...safeColors,
                primary: colors.teal,
                retro:   '#01B3EF',
                range:   '#F05F19',
                vert:    '#7DC353',
                            paper:   'rgb(var(--c-paper) / <alpha-value>)',
                surface: {
                    DEFAULT: 'rgb(var(--c-surface) / <alpha-value>)',
                    2:       'rgb(var(--c-surface-2) / <alpha-value>)',
                },
                ink: {
                    DEFAULT: 'rgb(var(--c-ink) / <alpha-value>)',
                    soft:    'rgb(var(--c-ink-soft) / <alpha-value>)',
                },
                muted: 'rgb(var(--c-muted) / <alpha-value>)',
                line: {
                    DEFAULT: 'rgb(var(--c-line) / <alpha-value>)',
                    strong:  'rgb(var(--c-line-strong) / <alpha-value>)',
                },
                accent:  'rgb(var(--c-accent) / <alpha-value>)',
                primary: {
                    DEFAULT: 'rgb(var(--c-primary) / <alpha-value>)',
                    hi:      'rgb(var(--c-primary-hi) / <alpha-value>)',
                },
                'on-primary': 'rgb(var(--c-on-primary) / <alpha-value>)',
                ok:            'rgb(var(--c-ok) / <alpha-value>)',
                warn: 'rgb(var(--c-warn) / <alpha-value>)',
            },
            fontFamily: {
                sans:    ['"Instrument Sans"', ...defaultTheme.fontFamily.sans],
                display: ['"Zilla Slab"', 'Georgia', 'serif'],
                mono:    ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            keyframes: {
                rise: {
                    '0%':   { opacity: '0', transform: 'translateY(8px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                stamp: {
                    '0%':   { opacity: '0', transform: 'rotate(8deg) scale(1.5)' },
                    '60%':  { opacity: '1' },
                    '100%': { opacity: '0.9', transform: 'rotate(8deg) scale(1)' },
                },
            },
            animation: {
                rise:  'rise 0.5s ease both',
                stamp: 'stamp 0.55s cubic-bezier(0.2, 1.4, 0.4, 1) both',
            },
            fontFamily: {
                sans: ['Exo', ...defaultTheme.fontFamily.sans],
            },
            height: {
                panel: 'calc(100vh - 1rem)',
            },
        },
    },

    // NOTE: the `variants` key is a Tailwind v2 API. In v3 every variant is
    // available on every utility automatically — the block does nothing and was
    // removed to silence the "unrecognised key" warning.

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
        function ({ addUtilities }) {
            addUtilities({
                '.break-before-page': { 'break-before': 'page' },
                '.break-after-page':  { 'break-after':  'page' },
            });
        },
    ],
};
