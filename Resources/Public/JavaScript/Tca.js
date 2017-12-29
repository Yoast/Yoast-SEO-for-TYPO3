/*global define, top, tx_yoast_seo, TYPO3*/

define(['jquery', './bundle', 'TYPO3/CMS/Backend/AjaxDataHandler', 'TYPO3/CMS/Backend/Notification', 'TYPO3/CMS/Backend/PageActions'], function ($, YoastSEO, AjaxDataHandler, Notification, PageActions) {
    'use strict';

    var previewRequest = $.get(previewUrl);

    $(function () {
        var $targetElement = $('#' + previewTargetId);

        previewRequest.done(function (previewDocument) {
            var $snippetPreview = $targetElement.append('<div class="snippetPreview yoastPanel" />').find('.snippetPreview');

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
                    metaDesc: $metaSection.find('description').text(),
                    urlPath: $metaSection.find('pathOverride').text()
                },
                baseURL: $metaSection.find('url').text().replace($metaSection.find('slug').text(), '/'),
                placeholder: {
                    urlPath: $metaSection.find('slug').text().replace(/^\/|\/$/g, '')
                },
                targetElement: $snippetPreview.get(0),
                callbacks: {
                }
            });

            var app = new YoastSEO.App({
                snippetPreview: snippetPreview,
                targets: {
                },
                callbacks: {
                    getData: function () {
                        return {
                            title: $metaSection.find('title').text(),
                            text: pageContent
                        };
                    },
                    saveScores: function (score) {
                    },
                    saveContentScore: function (score) {
                    }
                },
                locale: $metaSection.find('locale').text()
            });
        });

        previewRequest.fail(function (jqXHR) {
            var text = 'We got an error ' + jqXHR.status + ' (' + jqXHR.statusText + ') when requesting ' + previewUrl + ' to analyse your content. Please check your javascript console for more information.';
            var shortText = 'We got an error ' + jqXHR.status + ' (' + jqXHR.statusText + ') when analysing your content';

            Notification.error('Loading the page content preview failed', shortText, 5);

            $targetElement.find('.spinner').hide();
            $targetElement.html('<div class="callout callout-warning">' + text + '</div>');
        });
    });
});