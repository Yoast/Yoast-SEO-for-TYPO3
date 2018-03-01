var oldDefineAmd = define.amd;
define.amd = false;
var SnippetPreview = require( "yoastseo" ).SnippetPreview;
var App = require( "yoastseo" ).App;
var Paper = require( "yoastseo" ).value;
var Researcher = require( "yoastseo" ).Researcher;
define.amd = oldDefineAmd;
var scoreToRating = require( 'yoastseo' ).helpers.scoreToRating;
var debounce = require('lodash/debounce');

define(function () {
    return {
        'App': App,
        'debounce': debounce,
        'Paper': Paper,
        'SnippetPreview': SnippetPreview,
        'Researcher': Researcher,
        'scoreToRating': scoreToRating
    };
});