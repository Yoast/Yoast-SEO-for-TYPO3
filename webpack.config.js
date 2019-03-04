const path = require('path');
const webpack = require('webpack');

const PORT = 3333;

module.exports = {
    entry: {
        plugin: ['@babel/polyfill', './Resources/Public/JavaScript/plugin.js'],
        /*worker: './Resources/Public/JavaScript/worker.js'*/
    },
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, 'Resources/Public/JavaScript/dist'),
        publicPath: `https://localhost:${PORT}/typo3conf/ext/yoast_seo/Resources/Public/JavaScript/dist/`
    },
    module: {
        rules: [
            {
                test: /\.jsx?$/,
                use: [{
                    loader: 'babel-loader'
                }],
            },
        ],
    },
    devServer: {
        inline: true,
        host: '0.0.0.0',
        public: `localhost:${PORT}`,
        disableHostCheck: true,
        port: PORT,
        historyApiFallback: true,
        hot: true,
        publicPath: '/typo3conf/ext/yoast_seo/Resources/Public/JavaScript/dist/',
        https: true,
        headers: {
            "Access-Control-Allow-Origin": "*",
            "Access-Control-Allow-Credentials": "true",
            "Access-Control-Allow-Headers": "Content-Type, Authorization, x-id, Content-Length, X-Requested-With",
            "Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, OPTIONS"
        }
    }
};