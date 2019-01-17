const path = require('path');

module.exports = {
    entry: {
        plugin: './Resources/Public/JavaScript/plugin.js',
        worker: './Resources/Public/JavaScript/worker.js'
    },
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, 'Resources/Public/JavaScript/dist')
    }
};