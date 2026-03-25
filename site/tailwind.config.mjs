/** @type {import('tailwindcss').Config} */
export default {
  content: ['./src/**/*.{astro,html,js,jsx,ts,tsx}'],
  theme: {
    extend: {
      colors: {
        brand: {
          50:  '#fdf4f5',
          100: '#fbe5e8',
          200: '#f5c2c9',
          300: '#ec8f9b',
          400: '#df5869',
          500: '#c73348',
          600: '#9b2335',
          700: '#7d1c2a',
          800: '#681921',
          900: '#57181d',
          950: '#31080d',
        },
        warm: {
          50:  '#faf8f5',
          100: '#f3ede6',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        display: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
};
