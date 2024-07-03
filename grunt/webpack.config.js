const path = require('path');
const webpack = require('webpack');

const PORT = 3333;

module.exports = {
    entry: {
        plugin: ['@babel/polyfill', './../Resources/Public/JavaScript/plugin.js'],
        worker: ['@babel/polyfill', './../Resources/Public/JavaScript/worker.js']
    },
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, '../Resources/Public/JavaScript/dist'),
        publicPath: `https://localhost:${PORT}/typo3conf/ext/yoast_seo/Resources/Public/JavaScript/dist/`
    },
    resolve: {
        modules: [path.join(__dirname, 'node_modules')],
        fallback: {
            "url": require.resolve("url/"),
            "util": require.resolve("util/")
        },
    },
    plugins: [
        // fix "process is not defined" error:
        new webpack.ProvidePlugin({
            process: 'process/browser.js',
        }),
    ],
    module: {
        rules: [
            {
                test: /\.jsx?$/,
                use: [{
                    loader: 'babel-loader'
                }],
            },
            {
                test: /\.css?$/,
                use: [{
                    loader: 'css-loader'
                }]
            }
        ],
    },
    devServer: {
        host: '0.0.0.0',
        client: {
            webSocketURL: `auto://localhost:${PORT}/ws`
        },
        allowedHosts: "all",
        port: PORT,
        historyApiFallback: true,
        hot: true,
        static: {
            publicPath: '/typo3conf/ext/yoast_seo/Resources/Public/JavaScript/dist/',
        },
        server: 'https',
        headers: {
            "Access-Control-Allow-Origin": "*",
            "Access-Control-Allow-Credentials": "true",
            "Access-Control-Allow-Headers": "Content-Type, Authorization, x-id, Content-Length, X-Requested-With",
            "Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, OPTIONS"
        }
    }
};
