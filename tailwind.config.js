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