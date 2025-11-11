/** @type {import('tailwindcss').Config} */

const colors = require('tailwindcss/colors')

module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        primery: colors.fuchsia
      }
    },
  },
  plugins: [
    // require('@tailwindcss/forms'),
  ],
}

