const path = require("path");

module.exports = {
    pluginOptions: {
        "style-resources-loader": {
            preProcessor: "scss",
            patterns: [path.resolve(__dirname, "./src/styles/styles.scss")]
        },
        i18n: {
            locale: "en",
            fallbackLocale: "en",
            localeDir: "locales",
            enableInSFC: false
        }
    },
    configureWebpack: {
        resolve: {
            alias: {
                "@": path.resolve(__dirname, './src')
            },
            extensions: ['.js', '.vue', '.json']
        },
        devtool: 'eval-source-map'
    },
    chainWebpack: config => {
        config.module
            .rule('graphql')
            .test(/\.(graphql|gql)$/)
            .use('graphql-tag/loader')
            .loader('graphql-tag/loader')
            .end();
        config
            .plugin('html')
            .tap(args => {
                args[0].title = "Transport manager";
                return args;
            });
    },
    //devServer: { https: true }
};
