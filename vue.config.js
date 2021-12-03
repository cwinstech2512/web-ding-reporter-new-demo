const webpack = require('webpack');

module.exports = {
  // head: [
  //   ['link', {
  //     rel: 'stylesheet',
  //     href: `https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css`
  //   }],
  // ],
  transpileDependencies: [
    'vuetify'
  ],
  configureWebpack: {
    plugins: [
      new webpack.ProvidePlugin({
        $: 'jquery',
        jQuery: 'jquery',
        'windows.jQuery': 'jquery',
      }),
    ],
  },
}
