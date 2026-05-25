/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      /* ── Brand Colors ── */
      colors: {
        primary: {
          DEFAULT: '#6C5CE7',
          50:  '#F5F3FF',
          100: '#EDE9FE',
          200: '#DDD6FE',
          300: '#C4B5FD',
          400: '#A78BFA',
          500: '#6C5CE7',
          600: '#5B4BD6',
          700: '#4C3CC5',
          800: '#3D2DB4',
          900: '#2E1FA3',
        },
        secondary: {
          DEFAULT: '#A855F7',
          50:  '#FAF5FF',
          100: '#F3E8FF',
          200: '#E9D5FF',
          300: '#D8B4FE',
          400: '#C084FC',
          500: '#A855F7',
          600: '#9333EA',
          700: '#7E22CE',
          800: '#6B21A8',
          900: '#581C87',
        },
        accent: {
          DEFAULT: '#FF4DB6',
          light: '#FF85D0',
          dark: '#E6369E',
        },
        warm: {
          DEFAULT: '#FFC67D',
          light: '#FFD9A8',
          dark: '#E6A654',
        },
        success: {
          DEFAULT: '#22C55E',
          light: '#86EFAC',
          dark: '#16A34A',
        },
        danger: {
          DEFAULT: '#EF4444',
          light: '#FCA5A5',
          dark: '#DC2626',
        },
        warning: {
          DEFAULT: '#F59E0B',
          light: '#FCD34D',
          dark: '#D97706',
        },
        neutral: {
          light: '#F8F8FC',
          dark: '#0F0F14',
        },
        surface: {
          DEFAULT: '#FFFFFF',
          50:  '#FAFAFA',
          100: '#F5F5F5',
          200: '#E5E5E5',
          300: '#D4D4D4',
          400: '#A3A3A3',
          500: '#737373',
          600: '#525252',
          700: '#404040',
          800: '#262626',
          900: '#171717',
        },
      },

      /* ── Typography ── */
      fontFamily: {
        sans: ['"Sequel Sans"', '"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
        display: ['"Sequel Sans"', '"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
      },
      fontSize: {
        'h1': ['40px', { lineHeight: '48px', fontWeight: '600' }],
        'h2': ['28px', { lineHeight: '36px', fontWeight: '600' }],
        'h3': ['22px', { lineHeight: '28px', fontWeight: '600' }],
        'h4': ['18px', { lineHeight: '24px', fontWeight: '500' }],
        'body-lg': ['16px', { lineHeight: '24px', fontWeight: '400' }],
        'body': ['14px', { lineHeight: '20px', fontWeight: '400' }],
        'caption': ['12px', { lineHeight: '16px', fontWeight: '400' }],
      },

      /* ── Shadows ── */
      boxShadow: {
        'xs':    '0 1px 2px rgba(15, 15, 20, 0.04)',
        'sm':    '0 4px 12px rgba(15, 15, 20, 0.06)',
        'md':    '0 10px 30px rgba(15, 15, 20, 0.08)',
        'lg':    '0 20px 60px rgba(15, 15, 20, 0.12)',
        'glow':  '0 0 20px rgba(108, 92, 231, 0.3)',
        'glow-lg': '0 0 40px rgba(108, 92, 231, 0.2)',
      },

      /* ── Border Radius ── */
      borderRadius: {
        'xs': '4px',
        'sm': '8px',
        'md': '12px',
        'lg': '16px',
        'xl': '24px',
        'full': '9999px',
      },

      /* ── Spacing ── */
      spacing: {
        '4.5': '18px',
        '13': '52px',
        '15': '60px',
        '17': '68px',
        '18': '72px',
        '22': '88px',
        '26': '104px',
        '30': '120px',
      },

      /* ── Animations ── */
      keyframes: {
        'fade-in-up': {
          '0%': { opacity: '0', transform: 'translateY(20px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        'fade-in': {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        'slide-up': {
          '0%': { transform: 'translateY(100%)' },
          '100%': { transform: 'translateY(0)' },
        },
        'slide-down': {
          '0%': { transform: 'translateY(-20px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        'scale-in': {
          '0%': { transform: 'scale(0.95)', opacity: '0' },
          '100%': { transform: 'scale(1)', opacity: '1' },
        },
        'shimmer': {
          '0%': { backgroundPosition: '-200% 0' },
          '100%': { backgroundPosition: '200% 0' },
        },
        'pulse-dot': {
          '0%, 100%': { opacity: '1', transform: 'scale(1)' },
          '50%': { opacity: '0.5', transform: 'scale(1.5)' },
        },
        'marquee': {
          '0%': { transform: 'translateX(0)' },
          '100%': { transform: 'translateX(-50%)' },
        },
        'float': {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-10px)' },
        },
        'draw-line': {
          '0%': { strokeDashoffset: '1000' },
          '100%': { strokeDashoffset: '0' },
        },
        'shake': {
          '0%, 100%': { transform: 'translateX(0)' },
          '10%, 30%, 50%, 70%, 90%': { transform: 'translateX(-4px)' },
          '20%, 40%, 60%, 80%': { transform: 'translateX(4px)' },
        },
        'skeleton': {
          '0%': { backgroundPosition: '-200px 0' },
          '100%': { backgroundPosition: 'calc(200px + 100%) 0' },
        },
        'spin-slow': {
          '0%': { transform: 'rotate(0deg)' },
          '100%': { transform: 'rotate(360deg)' },
        },
        'toast-in': {
          '0%': { transform: 'translateX(100%)', opacity: '0' },
          '100%': { transform: 'translateX(0)', opacity: '1' },
        },
        'toast-out': {
          '0%': { transform: 'translateX(0)', opacity: '1' },
          '100%': { transform: 'translateX(100%)', opacity: '0' },
        },
        'confetti': {
          '0%': { transform: 'translateY(-100vh) rotate(0deg)', opacity: '1' },
          '100%': { transform: 'translateY(100vh) rotate(720deg)', opacity: '0' },
        },
      },
      animation: {
        'fade-in-up': 'fade-in-up 0.5s ease-out both',
        'fade-in': 'fade-in 0.3s ease-out both',
        'slide-up': 'slide-up 0.4s cubic-bezier(0.16, 1, 0.3, 1) both',
        'slide-down': 'slide-down 0.3s ease-out both',
        'scale-in': 'scale-in 0.3s cubic-bezier(0.16, 1, 0.3, 1) both',
        'shimmer': 'shimmer 2s infinite linear',
        'pulse-dot': 'pulse-dot 2s ease-in-out infinite',
        'marquee': 'marquee 30s linear infinite',
        'float': 'float 6s ease-in-out infinite',
        'shake': 'shake 0.5s ease-in-out',
        'skeleton': 'skeleton 1.5s ease-in-out infinite',
        'spin-slow': 'spin-slow 3s linear infinite',
        'toast-in': 'toast-in 0.4s cubic-bezier(0.16, 1, 0.3, 1) both',
        'toast-out': 'toast-out 0.3s ease-in both',
        'confetti': 'confetti 3s ease-in-out infinite',
      },

      /* ── Backdrop Blur ── */
      backdropBlur: {
        'xs': '4px',
      },

      /* ── Z-Index ── */
      zIndex: {
        '60': '60',
        '70': '70',
        '80': '80',
        '90': '90',
        '100': '100',
      },
    },
  },
  plugins: [],
};
