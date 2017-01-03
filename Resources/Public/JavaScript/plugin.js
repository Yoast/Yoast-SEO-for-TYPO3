var SnippetPreview = require( "yoastseo" ).SnippetPreview;
var App = require( "yoastseo" ).App;
var scoreToRating = require( 'yoastseo' ).helpers.scoreToRating;
var debounce = require('lodash/debounce');

define(function () {
    return {
        'App': App,
        'debounce': debounce,
        'SnippetPreview': SnippetPreview,
        'scoreToRating': scoreToRating
    };
});