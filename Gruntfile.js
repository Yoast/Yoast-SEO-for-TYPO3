module.exports = function(grunt) {
    'use strict';

    require('time-grunt')(grunt);

    // Load Grunt configurations and tasks
    require( 'load-grunt-config' )(grunt, {
        configPath: require( 'path' ).join( process.cwd(), 'grunt/config/' )
    });
};