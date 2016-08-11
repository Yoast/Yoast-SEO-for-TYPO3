var SnippetPreview = require( "yoastseo" ).SnippetPreview;
var debounce = require('lodash/debounce');
var App = require( "yoastseo" ).App;
var scoreToRating = require( 'yoastseo' ).helpers.scoreToRating;

var $ = TYPO3.jQuery;

$.getJSON(document.querySelector('[data-yoast-previewdataurl]').getAttribute('data-yoast-previewdataurl'), function (data) {
    var recordId = document.querySelector('[data-yoast-previewdataurl]').getAttribute('data-yoast-recordid');

    var snippetPreview = new SnippetPreview({
        data: {
            title: data.meta.title,
            metaDesc: data.meta.description
        },
        baseURL: data.meta.url,
        placeholder: {
            urlPath: ''
        },
        targetElement: document.getElementById('snippet'),
        callbacks: {
            saveSnippetData: debounce(function (data) {
                var payload = {
                    data: {
                        pages: {}
                    }
                };

                payload.data.pages[recordId] = {
                    title: data.title,
                    description: data.metaDesc
                };

                $.get(TYPO3.settings.ajaxUrls.record_process, payload);
            }, 1000)
        }
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