module.exports = {
  content: [
      './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
      './storage/framework/views/*.php',
      './resources/views/**/*.blade.php',
      './resources/js/**/*.js',
      './node_modules/flowbite/**/*.js' // <--- ДОБАВЬТЕ ЭТОТ ПУТЬ
  ],
  darkMode: 'class',
  theme: {
      extend: {
          colors: {
              primary: {"50":"#fff1f2","100":"#ffe4e6","200":"#fecdd3","300":"#fda4af","400":"#fb7185","500":"#f43f5e","600":"#e11d48","700":"#be123c","800":"#9f1239","900":"#881337","950":"#4c0519"}
            }
      },
      fontFamily: {
          'body': [
              'Roboto',
              'sans-serif',
          ],
          'sans': [
              'Roboto',
              'sans-serif',
          ]
      }
  },
  plugins: [
      require('@tailwindcss/forms'), // Если используете
      require('flowbite/plugin') // <--- ДОБАВЬТЕ ЭТОТ ПЛАГИН
  ],
}
