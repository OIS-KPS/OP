/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./coordinator/**/*.php",
    "./student/**/*.php",
    "./supervisor/**/*.php",
    "./src/**/*.php",
    "./components/**/*.php",
    "./public/**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        brand: '#0F2854',
      }
    },
  },
  plugins: [],
}