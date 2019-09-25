module.exports = {
  theme: {
    extend: {
      screens: {
        xxl: '1600px',
      },
      inset: (theme, { negative }) => ({
        'full': '100%',
        ...theme('spacing'),
        ...negative(theme('spacing')),
      }),
      maxWidth: (theme) => ({
        ...theme('spacing'),
      }),
      minHeight: theme => ({
        ...theme('spacing'),
        '25': '25vh',
        '50': '50vh',
        '75': '75vh',
      }),
    }
  },
  variants: {
  }
}