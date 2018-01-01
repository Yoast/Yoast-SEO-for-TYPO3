/*global define, top, tx_yoast_seo, TYPO3*/

define(['jquery', './bundle', 'TYPO3/CMS/Backend/AjaxDataHandler', 'TYPO3/CMS/Backend/Notification', 'TYPO3/CMS/Backend/PageActions'], function ($, YoastSEO, AjaxDataHandler, Notification, PageActions) {
    'use strict';

    var previewRequest = $.get(tx_yoast_seo.previewUrl);

    $(function () {
        var $targetElement = $('#' + tx_yoast_seo.previewTargetId);
        if ($("*[data-formengine-input-name='" + $titleTcaSelector + "']").parents('.form-wizards-element').length) {
            $("*[data-formengine-input-name='" + $titleTcaSelector + "']").parents('.form-wizards-element').append("<div class='yoast-progressbars-container'><progress id='yoast-progress-title' class='yoast-progressbars'></progress></div>");
        } else {
            $("*[data-formengine-input-name='" + $titleTcaSelector + "']").parents('.form-control-wrap').append("<div class='yoast-progressbars-container'><progress id='yoast-progress-title' class='yoast-progressbars'></progress></div>");
        }

        $("*[data-formengine-input-name='" + $descriptionTcaSelector + "']").after("<div class='yoast-progressbars-container'><progress id='yoast-progress-description' class='yoast-progressbars'></progress></div>");

        previewRequest.done(function (previewDocument) {
            var $snippetPreviewElement = $targetElement.append('<div class="snippetPreview yoastPanel" />').find('.snippetPreview');

            // the preview is an XML document, for easy traversal convert it to a jQuery object
            var $previewDocument = $(previewDocument);
            var $metaSection = $previewDocument.find('meta');
            var $contentElements = $previewDocument.find('content>element');

            var $focusKeywordElement = '';
            if (tx_yoast_seo.focusKeywordField) {
                $focusKeywordElement = $("*[data-formengine-input-name='" + tx_yoast_seo.focusKeywordField + "']");
            }

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
                    title: $metaSection.find('title').text(),
                    urlPath: $metaSection.find('slug').text().replace(/^\/|\/$/g, '')
                },
                targetElement: $snippetPreviewElement.get(0),
                callbacks: {
                    saveSnippetData: function() {
                        setTimeout(function() {
                            updateProgressBars(snippetPreview);
                        }, 500);
                    }
                }
            });

            $("*[data-formengine-input-name='" + $titleTcaSelector + "']").attr('placeholder', $metaSection.find('title').text());

            $focusKeywordElement.on('input', function() {
                snippetPreview.changedInput.bind( snippetPreview );
            } );

            var app = new YoastSEO.App({
                snippetPreview: snippetPreview,
                elementTargets: [
                    'id-of-title-element'
                ],
                targets: {
                    output: 'yoastseo-analysis-focuskeyword',
                    contentOutput: 'yoastseo-analysis-readability'
                },
                callbacks: {
                    getData: function () {
                        console.log('getData');
                        return {
                            title: $metaSection.find('title').text(),
                            text: pageContent,
                            keyword: $focusKeywordElement.val()
                        };
                    },
                    saveScores: function (score) {
                    },
                    saveContentScore: function (score) {
                    }
                },
                locale: $metaSection.find('locale').text(),
                translations: (window.tx_yoast_seo !== undefined && window.tx_yoast_seo !== null && window.tx_yoast_seo.translations !== undefined ? window.tx_yoast_seo.translations : null)
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

            $("*[data-formengine-input-name='" + $titleTcaSelector + "']").on('focus', updateProgressBars(snippetPreview));
            $("*[data-formengine-input-name='" + $descriptionTcaSelector + "']").on('focus', updateProgressBars(snippetPreview));

            $('.snippet-editor__view-toggle').on('click', function() {
                updateProgressBars(snippetPreview);
            });

            updateProgressBars(snippetPreview);
        });

        function updateProgressBars(snippetPreview) {
            snippetPreview.changedInput();

            $('#yoast-progress-title').attr('max', $('progress.snippet-editor__progress-title').attr('max'));
            $('#yoast-progress-title').attr('class', $('progress.snippet-editor__progress-title').attr('class'));
            $('#yoast-progress-title').addClass('yoast-progressbars');
            $('#yoast-progress-title').val($('progress.snippet-editor__progress-title').val());

            $('#yoast-progress-description').attr('max', $('progress.snippet-editor__progress-meta-description').attr('max'));
            $('#yoast-progress-description').attr('class', $('progress.snippet-editor__progress-meta-description').attr('class'));
            $('#yoast-progress-description').addClass('yoast-progressbars');
            $('#yoast-progress-description').val($('progress.snippet-editor__progress-meta-description').val());

            setTimeout(function() {
                updateProgressBars(snippetPreview);
            }, 2000);
        }

        previewRequest.fail(function (jqXHR) {
            var text = 'We got an error ' + jqXHR.status + ' (' + jqXHR.statusText + ') when requesting ' + tx_yoast_seo.previewUrl + ' to analyse your content. Please check your javascript console for more information.';
            var shortText = 'We got an error ' + jqXHR.status + ' (' + jqXHR.statusText + ') when analysing your content';

            Notification.error('Loading the page content preview failed', shortText, 5);

            $targetElement.find('.spinner').hide();
            $targetElement.html('<div class="callout callout-warning">' + text + '</div>');
        });
    });
});