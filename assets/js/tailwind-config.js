tailwind.config = {
  theme: {
    extend: {
      colors: {
        darkbg: '#0b0b0b',
        color: '#eee',
        accent: '#d32f2f',
        card: '#1E293B',
      },
      container: {
        center: true,
        padding: '1rem',
        screens: {
          sm: '600px',
          md: '728px',
          lg: '984px',
          xl: '1240px',
          '3xl': '1600px',
        },
      },
      fontSize: {
        clamp: 'clamp(0.875rem, 1vw + 0.5rem, 1rem)',
      },
      fontFamily: {
        sans: ['Poppins', 'sans-serif'],
      },
    },
  },
};
