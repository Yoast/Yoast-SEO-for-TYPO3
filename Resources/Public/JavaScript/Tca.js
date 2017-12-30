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
                    saveSnippetData: function() {
                        setTimeout(function() {
                            updateProgressBars();
                        }, 500);
                    }
                }
            });

            var app = new YoastSEO.App({
                snippetPreview: snippetPreview,
                elementTargets: [
                    'id-of-title-element'
                ],
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

            $("*[data-formengine-input-name='" + $titleTcaSelector + "']").on('input', function() {
                var $titleElement = $targetElement.find('#snippet-editor-title');
                $titleElement.val($(this).val());
                snippetPreview.changedInput();
            });

            $("*[data-formengine-input-name='" + $descriptionTcaSelector + "']").on('input', function() {
                var $descriptionElement = $targetElement.find('#snippet-editor-meta-description');
                $descriptionElement.val($(this).val());
                snippetPreview.changedInput();
            });

            $("*[data-formengine-input-name='" + $titleTcaSelector + "']").parent('.form-control-clearable').after("<progress id='yoast-progress-title' class='yoast-progressbars'></progress>");
            $("*[data-formengine-input-name='" + $descriptionTcaSelector + "']").after("<progress id='yoast-progress-description' class='yoast-progressbars'></progress>");

            updateProgressBars();

        });

        function updateProgressBars() {
            $('#yoast-progress-title').attr('max', $('progress.snippet-editor__progress-title').attr('max'));
            $('#yoast-progress-title').val($('progress.snippet-editor__progress-title').val());

            $('#yoast-progress-description').attr('max', $('progress.snippet-editor__progress-meta-description').attr('max'));
            $('#yoast-progress-description').val($('progress.snippet-editor__progress-meta-description').val());
        }

        previewRequest.fail(function (jqXHR) {
            var text = 'We got an error ' + jqXHR.status + ' (' + jqXHR.statusText + ') when requesting ' + previewUrl + ' to analyse your content. Please check your javascript console for more information.';
            var shortText = 'We got an error ' + jqXHR.status + ' (' + jqXHR.statusText + ') when analysing your content';

            Notification.error('Loading the page content preview failed', shortText, 5);

            $targetElement.find('.spinner').hide();
            $targetElement.html('<div class="callout callout-warning">' + text + '</div>');
        });
    });
});