module.exports = {
    options: {
        livereload: true
    },
    js: {
        files: [
            'Resources/Public/JavaScript/plugin.js'
        ],
        tasks: [
            'browserify'
        ]
    },
    sass: {
        files: 'Resources/Private/SCSS/YoastTYPO3.scss',
        tasks: [
            'build:sass'
        ]
    }
};