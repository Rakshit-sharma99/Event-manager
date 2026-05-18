import forms from '@tailwindcss/forms';

export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/View/Components/**/*.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Poppins', 'Satoshi', 'ui-sans-serif', 'system-ui'],
                display: ['Poppins', 'Inter', 'ui-sans-serif'],
            },
            colors: {
                eventra: {
                    night: '#03060c',
                    panel: '#07101c',
                    line: '#15273c',
                    blue: '#1687ff',
                    cyan: '#49d8ff',
                    rose: '#ff4fb8',
                    amber: '#ffb454',
                },
            },
            boxShadow: {
                glow: '0 0 44px rgba(22, 135, 255, .42)',
                soft: '0 24px 80px rgba(0, 0, 0, .38)',
            },
            animation: {
                float: 'float 7s ease-in-out infinite',
                pulseGlow: 'pulseGlow 2.8s ease-in-out infinite',
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-14px)' },
                },
                pulseGlow: {
                    '0%, 100%': { opacity: .6, filter: 'blur(0px)' },
                    '50%': { opacity: 1, filter: 'blur(1px)' },
                },
            },
        },
    },
    plugins: [forms],
};
