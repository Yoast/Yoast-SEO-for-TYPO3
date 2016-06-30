var SnippetPreview = require( "yoastseo" ).SnippetPreview;
var App = require( "yoastseo" ).App;
var scoreToRating = require( 'yoastseo' ).helpers.scoreToRating;

var focusKeywordField = document.getElementById( "focusKeyword" );
// var contentField = document.getElementById( "content" );

var $ = TYPO3.jQuery;

$.getJSON(document.querySelector('[data-yoast-previewdataurl]').getAttribute('data-yoast-previewdataurl'), function (data) {

    var snippetPreview = new SnippetPreview({
        data: {
            title: data.meta.title
        },
        targetElement: document.getElementById( "snippet" )
    });

    var app = new App({
        snippetPreview: snippetPreview,
        targets: {
            output: 'seo',
            contentOutput: 'readability'
        },
        callbacks: {
            getData: function () {
                return {
                    title: data.meta.title,
                    keyword: document.querySelector('[data-yoast-focuskeyword]').getAttribute('data-yoast-focuskeyword'),
                    text: data.meta.description
                };
            },
            saveScores: function (score) {
                TYPO3.jQuery('[data-controls="seo"]').find('.wpseo-score-icon').addClass(scoreToRating(score));
            },
            saveContentScore: function (score) {
                TYPO3.jQuery('[data-controls="readability"]').find('.wpseo-score-icon').addClass(scoreToRating(score));
            }
        }
    });

    app.refresh();
});

var toggles = document.querySelectorAll('[data-controls]');
for (var i = 0; i < toggles.length; i++) {
    toggles[i].addEventListener('click', function () {

        $(this).find('.fa-chevron-down, .fa-chevron-up').toggleClass('fa-chevron-down fa-chevron-up');
        document.getElementById(this.getAttribute('data-controls')).classList.toggle('yoastPanel__content--open');
    });
}

// focusKeywordField.addEventListener( 'change', app.refresh.bind( app ) );
// contentField.addEventListener( 'change', app.refresh.bind( app ) );