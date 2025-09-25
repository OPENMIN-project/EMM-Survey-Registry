module.exports = {
  prefix: '',
  important: false,
  separator: ':',
  corePlugins: {},
  purge: {
    // content: ['./resources/js/components/*.vue', './resources/views/**/*.blade.php']
  },
  theme: {
    columnCount: [2, 3, 4]
  },
  plugins: [
    function ({addUtilities}) {
      const newUtilities = {
        '.rotate-z-180': {
          transform: 'rotateZ(180deg)',
        },
      }

      addUtilities(newUtilities)
    },
    require('tailwindcss-multi-column')(),
    require('@tailwindcss/custom-forms'),
  ],
}
