module.exports = {
  theme: {
    screens: {
      '2xs': '375px',
      'xs': '480px',
      'sm': '600px',
      'md': '768px',
      'lg': '1024px',
      'xl': '1280px',
      '2xl': '1400px',
      '3xl': '1600px',
      '4xl': '1900px',
    },
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
          light: '#507192',
          default: '#2c3e50',
          dark: '#0e1419',
        },
        secondary: {
          light: '#5faee3',
          default: '#3498db',
          dark: '#217dbb',
        },
        tertiary: {
          light: '#36d278',
          default: '#27ae60',
          dark: '#1e8449',
        }
      },
      screens: {
        '2xl': '1600px',
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