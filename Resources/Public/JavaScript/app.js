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
            return '<div id="' + elementIdPrefix + '_' + type + '_panel" class="col-sm-6 ' + type + 'Panel yoastSeoPanel">'
                + '<h3 class="snippet-editor__heading" data-controls="' + type + '">'
                + '<a href="#"><span class="wpseo-score-icon"></span>'
                + '<span class="yoastPanel__title" data-panel-title>' + type + '</span>'
                + focusKeyword
                + '</a></h3>'
                + focusKeywordField
                + '<div id="' + elementIdPrefix + '_' + type + '_panel_content" data-panel-content class="yoastPanel__content"></div>'
                + '</div>';

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
            var $previewDocument = $(previewDocument);
            var $metaSection = $previewDocument.find('meta');
            var $contentElements = $previewDocument.find('content>element');

            var pageContent = '';

            $contentElements.each(function (index, element) {
                pageContent += element.textContent;
            });

            var $focusKeywordElement = $('#' + tx_yoast_seo.settings.targetElementId + '_focusKeyword');
            var snippetPreview = new YoastSEO.SnippetPreview({
                data: {
                    title: $metaSection.find('title').text(),
                    metaDesc: $metaSection.find('description').text(),
                    urlPath: $metaSection.find('pathOverride').text(),
                },
                baseURL: $metaSection.find('url').text().replace($metaSection.find('slug').text(), '/'),
                placeholder: {
                    urlPath: $metaSection.find('slug').text().replace(/^\/|\/$/g, '')
                },
                targetElement: $snippetPreview.get(0),
                callbacks: {
                }
            });

            $focusKeywordElement.on('input', snippetPreview.changedInput.bind( snippetPreview ) );

            var cssFile = '<link rel="stylesheet" type="text/css" href="/typo3conf/ext/yoast_seo/Resources/Public/CSS/yoast-seo.min.css?1513745911" media="all">';;

            $('.yoastSeo--small').find('.yoastSeoPanel').on('click', function() {
                var focusKeyword = '';
                if ($(this).find('.yoastPanel__focusKeyword').html()) {
                    focusKeyword = ': ' + $(this).find('.yoastPanel__focusKeyword').html();
                }

                var title = $(this).find('.yoastPanel__title').text() + focusKeyword;
                var content = cssFile + $(this).find('.yoastPanel__content').html();

                Modal.show(title, content);
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
                            keyword: $focusKeywordElement.val(),
                            text: pageContent
                        };
                    },
                    saveScores: function (score) {
                        $seoPanel.find('.wpseo-score-icon').first().removeClass('good ok bad')
                        $seoPanel.find('.wpseo-score-icon').first().addClass(YoastSEO.scoreToRating(score / 10));

                        if (tx_yoast_seo.settings.editable == 1) {
                            $seoPanel.find('[data-panel-focus-keyword]').text($focusKeywordElement.val());
                            $seoPanel.find('.fa-chevron-down').addClass('fa-chevron-up').removeClass('fa-chevron-down');
                            $seoPanel.find('.snippet-editor__heading').addClass('snippet-editor__heading--active');
                            $seoPanel.find('[data-panel-content]').addClass('yoastPanel__content--open');
                            $seoPanel.find('[data-panel-focus-keyword-field]').addClass('form-group__focusKeyword--open');
                        }
                    },
                    saveContentScore: function (score) {
                        $readabilityPanel.find('.wpseo-score-icon').first().addClass(YoastSEO.scoreToRating(score / 10));

                        if (tx_yoast_seo.settings.editable == 1) {
                            $readabilityPanel.find('.fa-chevron-down').addClass('fa-chevron-up').removeClass('fa-chevron-down');
                            $readabilityPanel.find('.snippet-editor__heading').addClass('snippet-editor__heading--active');
                            $readabilityPanel.find('[data-panel-content]').addClass('yoastPanel__content--open');
                        }

                    }
                },
                locale: $metaSection.find('locale').text(),
                translations: (window.tx_yoast_seo !== undefined && window.tx_yoast_seo !== null && window.tx_yoast_seo.translations !== undefined ? window.tx_yoast_seo.translations : null)
            });

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
            var text = 'We got an error ' + jqXHR.status + ' (' + jqXHR.statusText + ') when requesting ' + tx_yoast_seo.settings.preview + ' to analyse your content. Please check your javascript console for more information.';
            var shortText = 'We got an error ' + jqXHR.status + ' (' + jqXHR.statusText + ') when analysing your content';

            Notification.error('Loading the page content preview failed', shortText, 5);

            $targetElement.find('.spinner').hide();
            $targetElement.html('<div class="callout callout-warning">' + text + '</div>');
        });
    });
});