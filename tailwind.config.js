const colors = require('tailwindcss/colors');

module.exports = {
    content: ["./resources/js/**/*.html", "./resources/js/**/*.vue"],
    theme: {
        extend: {
            colors: {
                theme: colors.teal,
                danger: colors.red
            },
            screens: {
                'light': {'raw': '(prefers-color-scheme: light)'},
                'dark': {'raw': '(prefers-color-scheme: light)'},
                'dark-mode': {'raw': '(prefers-color-scheme: light)'},
            }
        }
    },
    variants: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio')
    ],
};
