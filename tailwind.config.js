/** @type {import('tailwindcss').Config} */

const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
  content: [
    'templates/**/*.html.twig',
    'assets/js/**/*.js',
    'assets/js/**/*.jsx',
    'assets/app.jsx'
  ],
  theme: {
    extend: {
      backgroundImage: {
        'facebook': "url('/public/icons/social/facebook.svg')",
        'instagram': "url('/public/icons/social/instagram.svg')",
      },
      content: {
        'play': "url('/public/icons/play.svg')",
        'out': "url('/public/icons/out.svg')",
        'top': "url('/public/icons/top.svg')",
        'down': "url('/public/icons/down.svg')",
        'info': "url('/public/icons/info.svg')",
        'error': "url('/public/icons/error.svg')",
        'edit-img': "url('/public/icons/edit-img.svg')",
        'next': "url('/public/icons/next.svg')",
        'prev': "url('/public/icons/prev.svg')",
        'last': "url('/public/icons/last.svg')",
        'first': "url('/public/icons/first.svg')",
        'reply': "url('/public/icons/reply.svg')",
        '': "",
      }
    },
    fontFamily: {
      'sans': ['Noto Sans', ...defaultTheme.fontFamily.sans],
      'serif': defaultTheme.fontFamily.serif,
      'mono': defaultTheme.fontFamily.mono
    },
    borderRadius: {
      'none': '0',
      'sm': '0.125rem',
      DEFAULT: '0.130rem',
      'md': '0.375rem',
      'lg': '0.5rem',
      'full': '9999px',
      'large': '12px',
    },
    colors: {
      transparent: {
        DEFAULT: 'transparent',
        'purple': 'rgba(144,0,247,0.1)',
        'red': 'rgba(237,67,12,0.1)',
        'yellow': 'rgba(227,189,11,0.1)',
        'blue': 'rgba(12,136,237,0.1)',
        'gray': 'rgba(62,61,66,0.1)',
        'green': 'rgba(0,168,11,0.1)',
        'black': {
          'light': 'rgba(0,0,0,0.2)',
          DEFAULT: 'rgba(0,0,0,0.4)',
          'dark': 'rgba(0,0,0,0.8)',
        },
      },
      current: 'currentColor',
      'white': '#FEFEFE',
      'purple': {
        light: '#A32DFF',
        DEFAULT: '#9000F7',
        dark: '#7300C4',
        },
      'red': {
        light: '#F07B54',
        DEFAULT: '#ED430C',
        dark: '#D61600',
        },
      'yellow': {
        light: '#FAE500',
        DEFAULT: '#E3BD0B',
        dark: '#A88B08'
      },
      'blue': {
        light: '#54AAF0',
        DEFAULT: '#0C88ED',
        dark: '#053F6E',
      },
      'green': {
        light: '#44E14E',
        DEFAULT: '#00A80B',
        dark: '#005C06'
      },
      'gray': {
        '100': '#F2F2F2',
        '200': '#C7D1D9',
        '300': '#C6D0D8',
        '400': '#99ABC9',
        '500': '#3E3D42',
      }
    }
  },
  plugins: [
    require('@tailwindcss/typography'),
    require('@tailwindcss/forms'),
    require('@tailwindcss/line-clamp'),
    require('@tailwindcss/aspect-ratio'),
  ],
}
