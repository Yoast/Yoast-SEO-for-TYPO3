/*global define, top, tx_yoast_seo, TYPO3*/

define(['jquery', './bundle', 'TYPO3/CMS/Backend/AjaxDataHandler', 'TYPO3/CMS/Backend/Notification', 'TYPO3/CMS/Backend/PageActions', 'TYPO3/CMS/Backend/Modal'], function ($, YoastSEO, AjaxDataHandler, Notification, PageActions, Modal) {
   'use strict';

    var previewRequest = $.get(tx_yoast_seo.settings.preview);

    var buildYoastPanelMarkup = function (elementIdPrefix, type) {
        var focusKeyword = '';
        var focusKeywordField = '';

        if (type == 'seo') {
            focusKeyword = '<span class="yoastPanel__focusKeyword" data-panel-focus-keyword>' + tx_yoast_seo.settings.focusKeyword + '</span>';
            focusKeywordField = '<div class="form-group form-group__focusKeyword" data-panel-focus-keyword-field><label for="' + elementIdPrefix + '_focusKeyword">' + tx_yoast_seo.settings.focusKeywordLabel + '</label><input type="text" class="form-control" value="' + tx_yoast_seo.settings.focusKeyword + '" id="' + elementIdPrefix+ '_focusKeyword" name="tx_yoastseo_web_yoastseoseoplugin[focusKeyword]" /></div>';
        }

        if (tx_yoast_seo.settings.editable == 0) {
            return '';
        } else {
            return '<div id="' + elementIdPrefix + '_' + type + '_panel" class="yoastPanel ' + type + 'Panel">'
                + '<h3 class="snippet-editor__heading" data-controls="' + type + '">'
                + '<span class="wpseo-score-icon"></span>'
                + '<span class="yoastPanel__title" data-panel-title>' + type + '</span>'
                + focusKeyword
                + '<span class="fa fa-chevron-down"></span>'
                + '</h3>'
                + focusKeywordField
                + '<div id="' + elementIdPrefix + '_' + type + '_panel_content" data-panel-content class="yoastPanel__content"></div>'
                + '</div>';
        }
    };

    // make sure the document is ready before we interact with the DOM
    // use the jQuery (ready) callback
    $(function () {
        var $targetElement = $('#' + tx_yoast_seo.settings.targetElementId);

        previewRequest.done(function (previewDocument) {
            // wait with UI markup until the preview is loaded
            var $snippetPreview = $targetElement.append('<div class="snippetPreview yoastPanel" />').find('.snippetPreview');
            $targetElement.find('.spinner').hide();
            var $targetPanels = $targetElement.append('<div class="row" />').find('.row');
            if ($targetElement.hasClass('yoastSeo--small')) {
                var $readabilityPanel = $targetPanels.append(buildYoastPanelMarkup(tx_yoast_seo.settings.targetElementId, 'readability')).find('.readabilityPanel');
                var $seoPanel = $targetPanels.append(buildYoastPanelMarkup(tx_yoast_seo.settings.targetElementId, 'seo')).find('.seoPanel');
            } else {
                var $readabilityPanel = $targetElement.append(buildYoastPanelMarkup(tx_yoast_seo.settings.targetElementId, 'readability')).find('.readabilityPanel');
                var $seoPanel = $targetElement.append(buildYoastPanelMarkup(tx_yoast_seo.settings.targetElementId, 'seo')).find('.seoPanel');
            }

            // the CSS selector #snippet adds some margin to the panel
            $snippetPreview.attr('id', 'snippet');

            // the preview is an XML document, for easy traversal convert it to a jQuery object
            var pageContent = previewDocument.body;
            var $focusKeywordElement = $('#' + tx_yoast_seo.settings.targetElementId + '_focusKeyword');

            var snippetPreview = new YoastSEO.SnippetPreview({
                data: {
                    title: previewDocument.title,
                    metaDesc: previewDocument.description,
                    urlPath: previewDocument.slug
                },
                baseURL: previewDocument.baseUrl,
                placeholder: {
                    title: previewDocument.title,
                    urlPath: previewDocument.slug
                },
                targetElement: $snippetPreview.get(0),
                callbacks: {
                },
                previewMode: 'desktop'
            });

            $focusKeywordElement.on('input', snippetPreview.changedInput.bind( snippetPreview ) );

            var cssFile = '<link rel="stylesheet" type="text/css" href="/typo3conf/ext/yoast_seo/Resources/Public/CSS/yoast-seo.min.css?1513745911" media="all">';;

            var app = new YoastSEO.App({
                snippetPreview: snippetPreview,
                targets: {
                    output: 'yoastSeo-score-bar-focuskeyword-content',
                    contentOutput: 'yoastSeo-score-bar-readability-content'
                },
                callbacks: {
                    getData: function () {
                        return {
                            title: previewDocument.title,
                            keyword: tx_yoast_seo.settings.focusKeyword,
                            text: pageContent
                        };
                    },
                    saveScores: function (score) {
                        var scoreClass = YoastSEO.scoreToRating(score / 10);
                        var scoreTextual = tx_yoast_scores[scoreClass.toLowerCase()];

                        $('#yoastSeo-score-bar-focuskeyword').find('.wpseo-score-icon').first().removeClass('good ok bad');
                        $('#yoastSeo-score-bar-focuskeyword').find('.wpseo-score-icon').first().addClass(scoreClass);
                        $('#yoastSeo-score-bar-focuskeyword').find('.wpseo-score-textual').first().html(scoreTextual);

                        $('.yoastSeo-score-bar-item').on('click', function() {
                            var preContent = '';
                            if ($(this).attr('id') == 'yoastSeo-score-bar-focuskeyword' && tx_yoast_seo.settings.focusKeyword) {
                                preContent = '<div style="margin-bottom: 10px;"><strong>' + (app.i18n.dgettext('js-text-analysis', 'Focus keyword')) + '</strong>: ' + tx_yoast_seo.settings.focusKeyword + '</div>';
                            }

                            var title = $(this).find('.yoastSeo-score-bar-item--title').text();
                            var content = cssFile + preContent + $(this).find('.yoastSeo-score-bar-item--content').html();

                            Modal.show(title, content);
                        });
                    },
                    saveContentScore: function (score) {
                        var scoreClass = YoastSEO.scoreToRating(score / 10);
                        var scoreTextual = tx_yoast_scores[scoreClass.toLowerCase()];

                        $('#yoastSeo-score-bar-readability').find('.wpseo-score-icon').first().removeClass('good ok bad');
                        $('#yoastSeo-score-bar-readability').find('.wpseo-score-icon').first().addClass(scoreClass);
                        $('#yoastSeo-score-bar-readability').find('.wpseo-score-textual').first().html(scoreTextual);
                    }
                },
                locale: previewDocument.locale,
                translations: (window.tx_yoast_seo !== undefined && window.tx_yoast_seo !== null && window.tx_yoast_seo.translations !== undefined ? window.tx_yoast_seo.translations : null)
            });

            if (tx_yoast_seo.settings.cornerstone == 1) {
                app.switchAssessors(true);
            } else {
                app.switchAssessors(false);
            }

            $('h1.t3js-title-inlineedit').after('' +
                '<div class="yoastSeo-score-bar">' +
                '   <div class="yoastSeo-score-bar-item" id="yoastSeo-score-bar-readability">' +
                '       <span class="wpseo-score-icon"></span> <span class="yoastSeo-score-bar-item--title">' + (app.i18n.dgettext('js-text-analysis', 'Readability')) + '</span>: <span class="wpseo-score-textual">-</span>' +
                '       <div class="yoastSeo-score-bar-item--content" id="yoastSeo-score-bar-readability-content"></div>' +
                '   </div>' +
                '   <div class="yoastSeo-score-bar-item" id="yoastSeo-score-bar-focuskeyword">' +
                '       <span class="wpseo-score-icon"></span> <span class="yoastSeo-score-bar-item--title">' + (app.i18n.dgettext('js-text-analysis', 'SEO')) + '</span>: <span class="wpseo-score-textual">-</span>' +
                '       <div class="yoastSeo-score-bar-item--content" id="yoastSeo-score-bar-focuskeyword-content"></div>' +
                '   </div>' +
                '</div>');

            // after bootstrapping the app (with possible translations) update the title of both panels
            $readabilityPanel.find('[data-panel-title]').text((app.i18n.dgettext('js-text-analysis', 'Readability')));
            $seoPanel.find('[data-panel-title]').text((app.i18n.dgettext('js-text-analysis', 'Focus keyword')));
            $seoPanel.find('[data-panel-focus-keyword]').text($focusKeywordElement.val());

            $snippetPreview.find('.snippet-editor__label').each(function () {
                $(this).wrap('<div class="form-group"></div>')
                var $inputField = $(this).find('.snippet-editor__input').detach();

                $inputField.addClass('form-control').removeClass('snippet-editor__input');
                $inputField.attr('name', 'tx_yoastseo_web_yoastseoseoplugin[' + $inputField.attr('id') + ']');

                $(this).removeClass('snippet-editor__label');
                $(this).after($inputField);
            });

            $snippetPreview.find('.snippet-editor__progress').each(function () {
                var $prev = $(this).prev();
                $(this).appendTo($prev);
            });

            if (tx_yoast_seo.settings.disableSlug) {
                $snippetPreview.find('#snippet-editor-slug').parent().hide();
            }

            $snippetPreview.find('.snippet-editor__button').addClass('btn btn-default').removeClass('snippet-editor__button snippet-editor__edit-button snippet-editor__submit');
            app.refresh();

            // bind a click handler to the chevron icon of both panels
            $targetElement.not('.yoastSeo--small').find('.snippet-editor__heading').on('click', function () {
                var $panel = $(this).parent();
                $panel.find('.fa-chevron-down, .fa-chevron-up').toggleClass('fa-chevron-down fa-chevron-up');
                $panel.find('.snippet-editor__heading').toggleClass('snippet-editor__heading--active');
                $panel.find('[data-panel-content]').toggleClass('yoastPanel__content--open');
                $panel.find('[data-panel-focus-keyword-field]').toggleClass('form-group__focusKeyword--open');
            });

            $('.yoast-collapse').on('click', function() {
                $('#' + $(this).attr('data-collapse-target')).toggleClass('yoast-collapse--hidden');
                $(this).find('.icon-markup').toggleClass('yoast-collapse--rotated');

                document.cookie = "hideSnippetPreview=" + $('#' + $(this).attr('data-collapse-target')).hasClass('yoast-collapse--hidden');
            });

            var yoastCookie = document.cookie.split(';');
            var cookieName = 'hideSnippetPreview';
            for (var i = 0; i < yoastCookie.length; i++) {
                var cookie = yoastCookie[i].trim();
                if (cookie.indexOf(cookieName) == 0) {
                    var value = cookie.substring(cookieName.length + 1, cookie.length);
                    if (value) {
                        if (value == 'true') {
                            $('#' + $('.yoast-collapse').attr('data-collapse-target')).addClass('yoast-collapse--hidden');
                            $('.yoast-collapse').find('.icon-markup').addClass('yoast-collapse--rotated');
                        }
                        if (value == 'false') {
                            $('#' + $('.yoast-collapse').attr('data-collapse-target')).removeClass('yoast-collapse--hidden');
                            $('.yoast-collapse').find('.icon-markup').removeClass('yoast-collapse--rotated');
                        }
                    }
                }
            }
            // due to the wacky workaround in typo3_src-7.6.11/typo3/sysext/backend/Resources/Public/JavaScript/PageActions.js:143
            // and the prevention of event propagation forces us to observe EVERY click event
            // depending on the target the actual method is invoked
            $('#PageLayoutController').on('click', function (e) {
                var $trigger = $(e.target);
                var currentPageTitle = PageActions.elements.$pageTitle.text();

                if ($trigger.hasClass('btn')
                    && $trigger.parentsUntil('form').find('input').val() !== currentPageTitle
                ) {
                    // wait a short period of time to give the application chance to update the title indication
                    window.setTimeout(function () {
                        if (PageActions.elements.$pageTitle.text() !== currentPageTitle) {
                            snippetPreview.data.title = PageActions.elements.$pageTitle.text();

                            app.refresh();
                        }
                    }, 200);
                }
            });
        });

        $('div#snippet_title.snippet_container.snippet-editor__container').off('click');

        previewRequest.fail(function (jqXHR) {
            var text = 'We got an error ' + jqXHR.status + ' (' + jqXHR.statusText + ') when requesting <a href="' + tx_yoast_seo.settings.preview + '" target="_blank">' + tx_yoast_seo.settings.preview + '</a> to analyse your content. Please check your javascript console for more information.';

            $targetElement.find('.spinner').hide();
            $targetElement.html('<div class="callout callout-warning">' + text + '</div>');
        });
    });
});