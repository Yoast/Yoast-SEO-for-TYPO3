var SnippetPreview = require( "yoastseo" ).SnippetPreview;
var App = require( "yoastseo" ).App;
var scoreToRating = require( 'yoastseo' ).helpers.scoreToRating;

var $ = TYPO3.jQuery;

$.getJSON(document.querySelector('[data-yoast-previewdataurl]').getAttribute('data-yoast-previewdataurl'), function (data) {

    var snippetPreview = new SnippetPreview({
        data: {
            title: data.meta.title
        },
        baseURL: data.meta.url,
        placeholder: {
            urlPath: ''
        },
        targetElement: document.getElementById( 'snippet' )
    });

    var pageContent = '';
    $.each(data.content, function () {
        if (typeof this.bodytext === 'string' && this.bodytext !== '') {
            pageContent += this.bodytext;
        }
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
                    keyword: $('[data-yoast-focuskeyword]').attr('data-yoast-focuskeyword'),
                    text: pageContent
                };
            },
            saveScores: function (score) {
                $('[data-controls="seo"]').find('.wpseo-score-icon').addClass(scoreToRating(score / 10));
            },
            saveContentScore: function (score) {
                $('[data-controls="readability"]').find('.wpseo-score-icon').addClass(scoreToRating(score / 10));
            }
        }
    });

    app.refresh();
});

$('.yoastPanel').on('click', function () {
    $(this).find('.fa-chevron-down, .fa-chevron-up').toggleClass('fa-chevron-down fa-chevron-up');
    $(this).find('#' + $(this).find('[data-controls]').attr('data-controls')).toggleClass('yoastPanel__content--open');
});