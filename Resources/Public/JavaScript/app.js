/*global define, tx_yoast_seo, TYPO3*/

define(['jquery', './bundle', 'TYPO3/CMS/Backend/Notification'], function ($, YoastSEO, Notification) {
   'use strict';

    var previewRequest = $.get(tx_yoast_seo.settings.preview);

    var buildYoastPanelMarkup = function (elementIdPrefix, type) {
        return '<div id="' + elementIdPrefix + '_' + type + '_panel" class="yoastPanel ' + type + 'Panel">'
            + '<h3 class="snippet-editor__heading" data-controls="' + type + '">'
                + '<span class="wpseo-score-icon"></span>'
                + '<span data-panel-title>' + type + '</span>'
                + '<span class="fa fa-chevron-down"></span>'
            + '</h3>'
                + '<div id="' + elementIdPrefix + '_' + type + '_panel_content" data-panel-content class="yoastPanel__content"></div>'
            + '</div>';
    };

    // make sure the document is ready before we interact with the DOM
    // use the jQuery (ready) callback
    $(function () {
        var $targetElement = $('#' + tx_yoast_seo.settings.targetElementId);

        previewRequest.success(function (previewDocument) {
            // wait with UI markup until the preview is loaded
            var $snippetPreview = $targetElement.append('<div class="snippetPreview" />').find('.snippetPreview');
            var $readabilityPanel = $targetElement.append(buildYoastPanelMarkup(tx_yoast_seo.settings.targetElementId, 'readability')).find('.readabilityPanel');
            var $seoPanel = $targetElement.append(buildYoastPanelMarkup(tx_yoast_seo.settings.targetElementId, 'seo')).find('.seoPanel');

            // the CSS selector #snippet adds some margin to the panel
            $snippetPreview.attr('id', 'snippet');

            // the preview is an XML document, for easy traversal convert it to a jQuery object
            var $previewDocument = $(previewDocument);
            var $metaSection = $previewDocument.find('meta');
            var $contentElements = $previewDocument.find('content>element');

            var pageContent = '';

            $contentElements.each(function (index, element) {
                pageContent += element.textContent;
            });

            var snippetPreview = new YoastSEO.SnippetPreview({
                data: {
                    title: $metaSection.find('title').text(),
                    metaDesc: $metaSection.find('description').text()
                },
                baseURL: $metaSection.find('url').text(),
                placeholder: {
                    urlPath: ''
                },
                targetElement: $snippetPreview.get(0)
            });

            var app = new YoastSEO.App({
                snippetPreview: snippetPreview,
                targets: {
                    output: $seoPanel.find('[data-panel-content]').attr('id'),
                    contentOutput: $readabilityPanel.find('[data-panel-content]').attr('id')
                },
                callbacks: {
                    getData: function () {
                        return {
                            title: $metaSection.find('title').text(),
                            keyword: tx_yoast_seo.settings.focusKeyword,
                            text: pageContent
                        };
                    },
                    saveScores: function (score) {
                        $seoPanel.find('.wpseo-score-icon').addClass(YoastSEO.scoreToRating(score / 10));
                    },
                    saveContentScore: function (score) {
                        $readabilityPanel.find('.wpseo-score-icon').addClass(YoastSEO.scoreToRating(score / 10));
                    }
                },
                locale: $metaSection.find('locale').text(),
                translations: (window.tx_yoast_seo !== undefined && window.tx_yoast_seo !== null && window.tx_yoast_seo.translations !== undefined ? window.tx_yoast_seo.translations : null)
            });

            // after bootstrapping the app (with possible translations) update the title of both panels
            $readabilityPanel.find('[data-panel-title]').text((app.i18n.dgettext('js-text-analysis', 'Readability')));
            $seoPanel.find('[data-panel-title]').text((app.i18n.dgettext('js-text-analysis', 'SEO')));

            app.refresh();

            // bind a click handler to the chevron icon of both panels
            $targetElement.find('.yoastPanel').on('click', function () {
                var $panel = $(this);
                $panel.find('.fa-chevron-down, .fa-chevron-up').toggleClass('fa-chevron-down fa-chevron-up');
                $panel.find('[data-panel-content]').toggleClass('yoastPanel__content--open');
            });
        });

        previewRequest.error(function (jqXHR) {
            Notification.error('Loading the page content preview failed', [jqXHR.status, jqXHR.statusText].join(' '), 0);
        });
    });
});