var SnippetPreview = require( "yoastseo" ).SnippetPreview;
var App = require( "yoastseo" ).App;
var scoreToRating = require( 'yoastseo' ).helpers.scoreToRating;

define(function () {
    return {
        'App': App,
        'SnippetPreview': SnippetPreview,
        'scoreToRating': scoreToRating
    };
});