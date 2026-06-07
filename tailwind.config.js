/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
    './resources/js/**/*.js',
  ],
  safelist: [
    {
      pattern: /^(bg|border|ring|text)-(indigo|teal|violet|amber|rose|emerald|sky)-(50|300|400|500|600|700)$/,
    },
    'text-indigo-600/70',
    'text-teal-600/70',
    'text-violet-600/70',
    'text-amber-600/70',
    'text-rose-600/70',
    'text-emerald-600/70',
    'text-sky-600/70',
  ],
  theme: {
    extend: {
      screens: {
        'xxl': '1920px',
      },
      colors: {
        gray: {
          100: '#f7fafc',
          200: '#edf2f7',
          300: '#e2e8f0',
          400: '#cbd5e0',
          500: '#a0aec0',
          600: '#718096',
          700: '#4a5568',
          800: '#2d3748',
          900: '#1a202c',
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ]
}