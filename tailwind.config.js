/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'bg-primary': '#1e1f2e',
        'bg-secondary': '#2d2f3f',
        'bg-card': '#383a4d',
        'bg-hover': '#424459',
        'bg-darker': '#2a2b3d',
        'accent-primary': '#00d9ff',
        'accent-cyan': '#00d9ff',
        'accent-success': '#00ff88',
        'accent-green': '#00ff88',
        'accent-warning': '#f59e0b',
        'accent-orange': '#f59e0b',
        'accent-danger': '#ef4444',
        'text-primary': '#ffffff',
        'text-secondary': '#a0a3bd',
        'text-muted': '#6b7280',
        'border-color': 'rgba(255, 255, 255, 0.1)',
      },
      boxShadow: {
        'lg': '0 4px 20px rgba(0, 0, 0, 0.4)',
        'md': '0 2px 10px rgba(0, 0, 0, 0.3)',
      },
    },
  },
  plugins: [],
}
