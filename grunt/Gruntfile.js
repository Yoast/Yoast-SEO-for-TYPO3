module.exports = function(grunt) {
    'use strict';

    require('time-grunt')(grunt);

    var project = {
        pkg: grunt.file.readJSON('package.json'),
        paths: {
            languages: './../Resources/Private/Language/'
        }
    };

    // Load Grunt configurations and tasks
    require( 'load-grunt-config' )(grunt, {
        configPath: require( 'path' ).join( process.cwd(), 'config' ),
        data: project
    });
};
