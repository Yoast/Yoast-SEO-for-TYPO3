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
                    title: $metaSection.find('pageTitle').text(),
                    urlPath: $metaSection.find('slug').text().replace(/^\/|\/$/g, '')
                },
                targetElement: $snippetPreviewElement.get(0),
                callbacks: {
                    saveSnippetData: function() {
                        updateProgressBars(snippetPreview);
                    }
                }
            });

            $("*[data-formengine-input-name='" + $titleTcaSelector + "']").attr('placeholder', $metaSection.find('pageTitle').text());

            $focusKeywordElement.on('input', function() {
                $('#yoastSeo-score-bar-focuskeyword-text').html($focusKeywordElement.val());

                app.getData();
                app.runAnalyzer();
                updateProgressBars(snippetPreview);
            } );

            var app = new YoastSEO.App({
                snippetPreview: snippetPreview,
                targets: {
                    output: 'yoastseo-analysis-focuskeyword',
                    contentOutput: 'yoastseo-analysis-readability'
                },
                callbacks: {
                    getData: function () {
                        return {
                            title: $metaSection.find('title').text(),
                            text: pageContent,
                            keyword: $focusKeywordElement.val()
                        };
                    },
                    saveScores: function (score) {
                        $('#yoastSeo-score-bar-focuskeyword').find('.wpseo-score-icon').first().removeClass('good ok bad');
                        $('#yoastSeo-score-bar-focuskeyword').find('.wpseo-score-icon').first().addClass(YoastSEO.scoreToRating(score / 10));
                        $('#yoastSeo-score-headline-focuskeyword').removeClass('good ok bad');
                        $('#yoastSeo-score-headline-focuskeyword').addClass(YoastSEO.scoreToRating(score / 10));
                    },
                    saveContentScore: function (score) {
                        $('#yoastSeo-score-bar-readability').find('.wpseo-score-icon').first().removeClass('good ok bad');
                        $('#yoastSeo-score-bar-readability').find('.wpseo-score-icon').first().addClass(YoastSEO.scoreToRating(score / 10));
                        $('#yoastSeo-score-headline-readability').removeClass('good ok bad');
                        $('#yoastSeo-score-headline-readability').addClass(YoastSEO.scoreToRating(score / 10));
                    }
                },
                locale: $metaSection.find('locale').text(),
                translations: (window.tx_yoast_seo !== undefined && window.tx_yoast_seo !== null && window.tx_yoast_seo.translations !== undefined ? window.tx_yoast_seo.translations : null)
            });

            $('form[name="editform"]').find('h1').after('<div class="yoastSeo-score-bar"><div class="yoastSeo-score-bar--item" id="yoastSeo-score-bar-readability"><span class="wpseo-score-icon"></span> ' + (app.i18n.dgettext('js-text-analysis', 'Readability')) + '</div><div class="yoastSeo-score-bar--item" id="yoastSeo-score-bar-focuskeyword"><span class="wpseo-score-icon"></span> ' + (app.i18n.dgettext('js-text-analysis', 'Focus keyword')) + ': <span id="yoastSeo-score-bar-focuskeyword-text">' + $focusKeywordElement.val() + '</span></div></div>');
            $('#yoastseo-analysis-focuskeyword').parents('.form-section').find('h4').prepend('<span class="wpseo-score-icon" id="yoastSeo-score-headline-focuskeyword"></span>');
            $('#yoastseo-analysis-readability').parents('.form-section').find('h4').prepend('<span class="wpseo-score-icon" id="yoastSeo-score-headline-readability"></span>');

            $("*[data-formengine-input-name='" + $titleTcaSelector + "']").on('input', function() {
                var $titleElement = $targetElement.find('#snippet-editor-title');
                var $newTitle = $metaSection.find('pageTitlePrepend').text() + $(this).val() + $metaSection.find('pageTitleAppend').text()
                $titleElement.val($newTitle.trim());

                snippetPreview.changedInput();

                setTimeout(function() {
                    app.getData();
                    app.runAnalyzer();
                }, 1000);
            });

            $("*[data-formengine-input-name='" + $descriptionTcaSelector + "']").on('input', function() {
                var $descriptionElement = $targetElement.find('#snippet-editor-meta-description');
                $descriptionElement.val($(this).val());
                snippetPreview.changedInput();

                app.getData();
                app.runAnalyzer();
            });

            $("*[data-formengine-input-name='" + $titleTcaSelector + "']").on('focus', updateProgressBars(snippetPreview));
            $("*[data-formengine-input-name='" + $descriptionTcaSelector + "']").on('focus', updateProgressBars(snippetPreview));

            $("a[role='tab']").on('click', function() {
                setTimeout(function() {
                    updateProgressBars(snippetPreview);
                }, 1000);
            });

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
        }

        function switchToYoast() {
            var sPageURL = window.location.search.substring(1);
            var sURLVariables = sPageURL.split('&');
            for (var i = 0; i < sURLVariables.length; i++)
            {
                var sParameterName = sURLVariables[i].split('=');
                if (sParameterName[0] == 'switchToYoast')
                {
                    var id = $targetElement.parents('.tab-pane').attr('id');
                    $('a[href="#' + id + '"]').tab('show');
                }
            }
        }

        switchToYoast();

        previewRequest.fail(function (jqXHR) {
            var text = 'We got an error ' + jqXHR.status + ' (' + jqXHR.statusText + ') when requesting <a href="' + tx_yoast_seo.previewUrl + '" target="_blank">' + tx_yoast_seo.previewUrl + '</a> to analyse your content. Please check your javascript console for more information.';

            $targetElement.find('.spinner').hide();
            $targetElement.html('<div class="callout callout-warning">' + text + '</div>');
        });
    });
});