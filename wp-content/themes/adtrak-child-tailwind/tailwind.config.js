module.exports = {
  theme: {
    fontFamily: {
      sans: [
        '-apple-system',
        'BlinkMacSystemFont',
        '"Segoe UI"',
        'Roboto',
        '"Helvetica Neue"',
        'Arial',
        '"Noto Sans"',
        'sans-serif',
        '"Apple Color Emoji"',
        '"Segoe UI Emoji"',
        '"Segoe UI Symbol"',
        '"Noto Color Emoji"',
      ],
      serif: [
        'Georgia',
        'Cambria',
        '"Times New Roman"',
        'Times',
        'serif',
      ],
    },    
    extend: {
      colors: {
        primary: {
          lighter: '#507192',
          default: '#2c3e50',
          darker: '#0e1419',
        },
        secondary: {
          lighter: '#5faee3',
          default: '#3498db',
          darker: '#217dbb',
        },
        tertiary: {
          lighter: '#36d278',
          default: '#27ae60',
          darker: '#1e8449',
        }
      },
      screens: {
        xxl: '1600px',
      },
      spacing: {
        '72': '18rem',
        '84': '21rem',
        '96': '24rem',
      },
      inset: (theme, { negative }) => ({
        'full': '100%',
        ...theme('spacing'),
        ...negative(theme('spacing')),
      }),
      maxWidth: (theme) => ({
        ...theme('spacing'),
      }),
      minHeight: (theme) => ({
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