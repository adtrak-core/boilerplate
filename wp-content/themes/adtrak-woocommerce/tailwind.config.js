module.exports = {
  theme: {
      screens: {
        '2xs' : '375px',
        'xs' : '480px',
        'sm' : '600px',
        'md' : '768px',
        'lg' : '1024px',
        'xl' : '1280px',
        '2xl' : '1400px',
        '3xl' : '1600px',
        '4xl' : '1900px',
    },
    fontFamily: {
      sans: ["Source Sans Pro", "sans-serif"],
    },
    filter: {
      none: "none",
      grayscale: "grayscale(1)",
    },
    extend: {
      colors: {
        primary: {
          light: "#eee",
          default: "#ccc",
          dark: "#666",
        },
        secondary: {
          light: "#f83",
          default: "#d0dfe5",
          dark: "#c0ced3",
        },
        // Overwrite Tailwind's gray, which has a tint of blue
        gray: {
          "100": "#f5f5f5",
          "200": "#eeeeee",
          "300": "#e0e0e0",
          "400": "#bdbdbd",
          "500": "#9e9e9e",
          "600": "#757575",
          "700": "#616161",
          "800": "#424242",
          "900": "#212121",
        },
      },
      screens: {
        "2xl": "1530px",
        "3xl": "1800px",
      },
      spacing: {
        "72": "18rem",
        "84": "21rem",
        "96": "24rem",
        "128": "32rem",
      },
      zIndex: {
        "-10": "-10",
        "-20": "-20",
      },
      inset: (theme, { negative }) => ({
        full: "100%",
        "1/2": "50%",
        ...theme("spacing"),
        ...negative(theme("spacing")),
      }),
      maxWidth: (theme) => ({
        ...theme("spacing"),
        ...theme("screens"),
      }),
      minHeight: (theme) => ({
        ...theme("spacing"),
        "25": "25vh",
        "50": "50vh",
        "75": "75vh",
      }),
    },
  },
  variants: {
    backgroundColor: ['responsive', 'group-hover', 'hover', 'focus', 'group-focus'],
    textColor: ['responsive', 'group-hover', 'hover', 'focus', 'group-focus'],
    padding: ['responsive', 'group-hover', 'hover', 'focus', 'group-focus'],
  },
  plugins: [
    require("tailwindcss-filters"), // https://github.com/benface/tailwindcss-filters
    require("tailwindcss-gradients"), //https://github.com/benface/tailwindcss-gradients
  ],
  corePlugins: {
    container: false,
  },
};
