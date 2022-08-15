const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors')

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: colors.teal
      },
      fontFamily: {
        sans: ['Exo', ...defaultTheme.fontFamily.sans],
      }
    },
  },
  plugins: [],
}
