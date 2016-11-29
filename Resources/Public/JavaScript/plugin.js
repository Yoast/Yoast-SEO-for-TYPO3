var SnippetPreview = require( "yoastseo" ).SnippetPreview;
var debounce = require('lodash/debounce');
var App = require( "yoastseo" ).App;
var scoreToRating = require( 'yoastseo' ).helpers.scoreToRating;

var $ = TYPO3.jQuery;

$.get(document.querySelector('[data-yoast-previewdataurl]').getAttribute('data-yoast-previewdataurl'), function (previewDocument) {
    var recordId = document.querySelector('[data-yoast-previewdataurl]').getAttribute('data-yoast-recordid');
    var recordTable = document.querySelector('[data-yoast-previewdataurl]').getAttribute('data-yoast-recordtable');

    var $previewDocument = $(previewDocument);
    var $metaSection = $previewDocument.find('meta');
    var $contentElements = $previewDocument.find('content>element');

    var pageContent = '';

    $contentElements.each(function (index, element) {
        pageContent += element.textContent;
    });

    var snippetPreview = new SnippetPreview({
        data: {
            title: $metaSection.find('title').text(),
            metaDesc: $metaSection.find('description').text()
        },
        baseURL: $metaSection.find('url').text(),
        placeholder: {
            urlPath: ''
        },
        targetElement: document.getElementById('snippet'),
        callbacks: {
            saveSnippetData: debounce(function (data) {
                var payload = {
                    data: {}
                };

                payload.data[recordTable] = {};

                payload.data[recordTable][recordId] = {
                    title: data.title,
                    description: data.metaDesc
                };

                $.get(TYPO3.settings.ajaxUrls.record_process, payload);
            }, 1000)
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
                    title: $metaSection.find('title').text(),
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