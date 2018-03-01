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
                previewMode: 'desktop'
            });

            $("*[data-formengine-input-name='" + $titleTcaSelector + "']").attr('placeholder', $metaSection.find('pageTitle').text());

            var apps = [];
            var cnt = 0;
            var firstFocusKeyword = '';


            function initApps() {
                $($('.yoastSeo-analysis-focuskeyword').get().reverse()).each(function () {
                    var focusKeywordElement = $(this).closest('.row').find('input.form-control');
                    var focusKeyword = focusKeywordElement.val();

                    focusKeywordElement.attr('data-yoast-keyword', 'true');
                    focusKeywordElement.attr('data-yoast-app-iterator', cnt);

                    firstFocusKeyword = focusKeyword;

                    apps[cnt] = new YoastSEO.App({
                        snippetPreview: snippetPreview,
                        targets: {
                            output: $(this).attr('id'),
                            contentOutput: 'yoastseo-analysis-readability'
                        },
                        callbacks: {
                            getData: function () {
                                return {
                                    title: $metaSection.find('title').text(),
                                    text: pageContent,
                                    keyword: focusKeywordElement.val()
                                };
                            },
                            saveScores: function (score) {
                                var scoreClass = YoastSEO.scoreToRating(score / 10);
                                var scoreTextual = tx_yoast_scores[scoreClass.toLowerCase()];

                                $('#yoastSeo-score-headline-focuskeyword').removeClass('good ok bad');
                                $('#yoastSeo-score-headline-focuskeyword').addClass(scoreClass);

                                var tmpIconElement = focusKeywordElement.closest('.panel').find('.wpseo-score-icon').first();
                                tmpIconElement.removeClass('good ok bad');
                                tmpIconElement.addClass(scoreClass);

                                $('#yoastSeo-score-bar-focuskeyword').find('.wpseo-score-icon').first().removeClass('good ok bad');
                                $('#yoastSeo-score-bar-focuskeyword').find('.wpseo-score-icon').first().addClass(scoreClass);
                                $('#yoastSeo-score-bar-focuskeyword').find('.wpseo-score-textual').first().html(scoreTextual);
                            },
                            saveContentScore: function (score) {
                                var scoreClass = YoastSEO.scoreToRating(score / 10);
                                var scoreTextual = tx_yoast_scores[scoreClass.toLowerCase()];

                                $('#yoastSeo-score-headline-readability').removeClass('good ok bad');
                                $('#yoastSeo-score-headline-readability').addClass(scoreClass);
                                $('#yoastSeo-score-bar-readability').find('.wpseo-score-icon').first().removeClass('good ok bad');
                                $('#yoastSeo-score-bar-readability').find('.wpseo-score-icon').first().addClass(scoreClass);
                                $('#yoastSeo-score-bar-readability').find('.wpseo-score-textual').first().html(scoreTextual);
                            }
                        },
                        locale: $metaSection.find('locale').text(),
                        translations: (window.tx_yoast_seo !== undefined && window.tx_yoast_seo !== null && window.tx_yoast_seo.translations !== undefined ? window.tx_yoast_seo.translations : null)
                    });

                    cnt++;
                });

                if (apps.length === 0) {
                    apps[0] = new YoastSEO.App({
                        snippetPreview: snippetPreview,
                        targets: {
                            contentOutput: 'yoastseo-analysis-readability'
                        },
                        callbacks: {
                            getData: function () {
                                return {
                                    title: $metaSection.find('title').text(),
                                    text: pageContent,
                                    keyword: tx_yoast_seo.firstFocusKeyword
                                };
                            },
                            saveScores: function (score) {
                                var scoreClass = YoastSEO.scoreToRating(score / 10);
                                var scoreTextual = tx_yoast_scores[scoreClass.toLowerCase()];

                                $('#yoastSeo-score-bar-focuskeyword').find('.wpseo-score-icon').first().removeClass('good ok bad');
                                $('#yoastSeo-score-bar-focuskeyword').find('.wpseo-score-icon').first().addClass(scoreClass);
                                $('#yoastSeo-score-bar-focuskeyword').find('.wpseo-score-textual').first().html(scoreTextual);
                            },
                            saveContentScore: function (score) {
                                var scoreClass = YoastSEO.scoreToRating(score / 10);
                                var scoreTextual = tx_yoast_scores[scoreClass.toLowerCase()];

                                $('#yoastSeo-score-headline-readability').removeClass('good ok bad');
                                $('#yoastSeo-score-headline-readability').addClass(scoreClass);
                                $('#yoastSeo-score-bar-readability').find('.wpseo-score-icon').first().removeClass('good ok bad');
                                $('#yoastSeo-score-bar-readability').find('.wpseo-score-icon').first().addClass(scoreClass);
                                $('#yoastSeo-score-bar-readability').find('.wpseo-score-textual').first().html(scoreTextual);
                            }
                        },
                        locale: $metaSection.find('locale').text(),
                        translations: (window.tx_yoast_seo !== undefined && window.tx_yoast_seo !== null && window.tx_yoast_seo.translations !== undefined ? window.tx_yoast_seo.translations : null)
                    });
                }
            }

            initApps();

            $('*[data-yoast-trigger="true"]').trigger('dataReceived', [pageContent, $metaSection.find('locale').text()]);

            $('div[id*="tx_yoastseo_focuskeyword_premium"]').find('.panel').on('click', function () {
                setTimeout(function() {
                    initApps();
                }, 500);
            });

            $('div[id*="tx_yoastseo_focuskeyword_premium"]').find('.panel').each(function () {
                $(this).find('.icon-tcarecords-tx_yoast_seo_premium_focus_keywords-default .icon-markup').addClass('wpseo-score-icon').removeClass('icon-markup').html('');
            });

            $('input[data-yoast-keyword="true"].form-control').on('input', function() {
                updateApp(apps[$(this).attr('data-yoast-app-iterator')], snippetPreview);
            });

            $('form[name="editform"]').find('h1').after('<div class="yoastSeo-score-bar"><div class="yoastSeo-score-bar--item" id="yoastSeo-score-bar-readability"><span class="wpseo-score-icon"></span> ' + (apps[0].i18n.dgettext('js-text-analysis', 'Readability')) + ': <span class="wpseo-score-textual">-</span></div><div class="yoastSeo-score-bar--item" id="yoastSeo-score-bar-focuskeyword"><span class="wpseo-score-icon"></span> ' + (apps[0].i18n.dgettext('js-text-analysis', 'SEO')) + ': <span class="wpseo-score-textual">-</span></div></div>');

            $('#yoastseo-analysis-focuskeyword').parents('.form-section').find('h4').prepend('<span class="wpseo-score-icon" id="yoastSeo-score-headline-focuskeyword"></span>');
            $('#yoastseo-analysis-readability').parents('.form-section').find('h4').prepend('<span class="wpseo-score-icon" id="yoastSeo-score-headline-readability"></span>');

            $("*[data-formengine-input-name='" + $titleTcaSelector + "']").on('input', function() {
                var $titleElement = $targetElement.find('#snippet-editor-title');
                var $newTitle = $metaSection.find('pageTitlePrepend').text() + $(this).val() + $metaSection.find('pageTitleAppend').text()
                $titleElement.val($newTitle.trim());

                snippetPreview.changedInput();

                setTimeout(function() {
                    updateAllApps(apps, snippetPreview);
                }, 500);
            });

            $("*[data-formengine-input-name='" + $descriptionTcaSelector + "']").on('input', function() {
                var $descriptionElement = $targetElement.find('#snippet-editor-meta-description');
                $descriptionElement.val($(this).val());
                snippetPreview.changedInput();

                setTimeout(function() {
                    updateAllApps(apps, snippetPreview);
                }, 500);
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

        function updateApp(app, snippetPreview) {
            app.getData();
            app.runAnalyzer();
            updateProgressBars(snippetPreview);
        }

        function updateAllApps(apps, snippetPreview) {
            for (var i=apps.length; i>0; i--) {
                updateApp(apps[(i - 1)], snippetPreview);
            }
        }

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