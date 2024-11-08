const path = require("path");
const baseConfig  = require( "./webpack.config.base" );

const outputFilename = "[name].js";

module.exports = [
    baseConfig({
        entry: {
            plugin: './javascript/plugin.js',
            worker: './javascript/worker.js',
        },
        output: {
            filename: outputFilename,
            path: path.resolve(__dirname, "../../Resources/Public/JavaScript/dist"),
        },
    }),
    baseConfig({
        entry: {
            'yoast-seo-backend.min': './sass/backend-module.scss',
            'yoast.min': './sass/yoast.scss',
        },
        output: {
            filename: outputFilename,
            path: path.resolve(__dirname, "../../Resources/Public/CSS"),
        },
    }),
]
