const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
module.exports = {
  mode: 'development',
  entry: {
    'js/app' : './src/js/app.js',
    'js/inicio' : './src/js/inicio.js',
    'js/personalDot/index' : './src/js/personalDot/index.js',
    'js/prendasDot/index' : './src/js/prendasDot/index.js',
    'js/tallasDot/index' : './src/js/tallasDot/index.js',
    'js/inventarioDot/index' : './src/js/inventarioDot/index.js',
    'js/pedidosDot/index' : './src/js/pedidosDot/index.js',
    'js/usuario/index' : './src/js/usuario/index.js',
    'js/entregasDot/index' : './src/js/entregasDot/index.js',
    'js/asigPermisos/index' : './src/js/asigPermisos/index.js',
    'js/estadisticas/index' : './src/js/estadisticas/index.js',
    'js/permisos/index' : './src/js/permisos/index.js',
    'js/aplicaciones/index' : './src/js/aplicaciones/index.js',
    'js/login/index' : './src/js/login/index.js',
    'js/historial/index' : './src/js/historial/index.js'
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'public/build')
  },
  plugins: [
    new MiniCssExtractPlugin({
        filename: 'styles.css'
    })
  ],
  module: {
    rules: [
      {
        test: /\.(c|sc|sa)ss$/,
        use: [
            {
                loader: MiniCssExtractPlugin.loader
            },
            'css-loader',
            'sass-loader'
        ]
      },
      {
        test: /\.(png|svg|jpe?g|gif)$/,
        type: 'asset/resource',
      },
    ]
  }
};