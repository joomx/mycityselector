module.exports = {
    lintOnSave: false,
    css: {
        extract: false
    },
    configureWebpack: {
        optimization: {
            splitChunks: false
        },
        resolve: {
            extensions: ['.js', '.vue', '.json'],
            alias: {
                'vue$': 'vue/dist/vue.esm.js',
            }
        },
        output: {
            filename: 'build.js'
        }
    },
    filenameHashing: false,
    publicPath: '/modules/mod_mycityselector/tmpl/webpack/mcs-modal/dist'
}