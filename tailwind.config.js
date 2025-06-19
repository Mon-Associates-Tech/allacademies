const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors')

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/Http/Livewire/ExaminationHeading.php",
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
  ],
    darkMode: 'class',
  theme: {
    extend: {
      colors: {
        ...colors,
        primary: colors.teal,
        'retro': '#01B3EF',
        'range': '#F05F19',
        'vert' : '#7DC353',
      },
      fontFamily: {
        sans: ['Exo', ...defaultTheme.fontFamily.sans],
      },
      height: {
        panel: 'calc(100vh - 1rem)',
      },
    },
      variants:{
        extend: {
            width: ['sidebar-expanded'],
        }
      }
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio'),
      function({ addUtilities }) {
          addUtilities({
              '.break-before-page': {
                  'break-before': 'page',
              },
              '.break-after-page': {
                  'break-after': 'page',
              }
          })
      }
  ],
}
