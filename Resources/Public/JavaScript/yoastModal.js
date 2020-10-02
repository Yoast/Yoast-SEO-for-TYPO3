/*global define, top, TYPO3*/

define(['jquery', 'TYPO3/CMS/Backend/Modal'], function ($, Modal) {
  'use strict';

  let styling = '<style>' +
    'h3.yoast-modal-h3 { margin: 12px 0 12px; }' +
    'a.yoast-modal-link { text-decoration: underline; }' +
    'svg.yoast-modal-logo { height: 125px; width: 125px; float: right; margin: 0px 0px 16px 16px; }' +
    '</style>' +
    '<link rel="stylesheet" type="text/css" href="' + TYPO3.settings.Yoast.backendCssUrl + '">';

  $('*[data-yoast-modal-type="synonyms"]').on('click', function (e) {
    e.preventDefault();
    let content = styling +
      '<svg class="yoast-modal-logo" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 488.22"><path d="M436.82 4.06A90 90 0 0 0 410 0H90A90 90 0 0 0 0 90v270a90 90 0 0 0 90 90h410V90a90 90 0 0 0-63.18-85.94z" fill="#a4286a"></path><path d="M436.82 4.06L184.15 450H500V90a90 90 0 0 0-63.18-85.94z" fill="#6c2548"></path><path d="M74.4 339.22v34.93c21.63-.85 38.51-8 52.84-22.46 14.76-14.83 27.45-38 39.93-72.85l92.53-248H215l-74.6 207.07-37.09-116.15h-41l54.42 139.79a57.49 57.49 0 0 1 0 41.84c-5.52 14.2-15.35 30.88-42.33 35.83z" fill="#fff"></path><circle cx="368.33" cy="124.68" r="97.34" transform="rotate(-45 368.335 124.68)" fill="#9fda4f"></circle><path d="M416.2 39.93l-95.74 169.51A97.34 97.34 0 1 0 416.2 39.93z" fill="#77b227"></path><path d="M294.78 254.75l-.15-.08-.13-.07a63.6 63.6 0 0 0-62.56 110.76h.13a63.6 63.6 0 0 0 62.71-110.67z" fill="#fec228"></path><path d="M294.5 254.59l-62.56 110.76a63.6 63.6 0 1 0 62.56-110.76z" fill="#f49a00"></path><path d="M222.31 450.07A38.16 38.16 0 0 0 203 416.83a38.18 38.18 0 1 0 19.41 33.27z" fill="#ff4e47"></path><path d="M202.9 416.8l-37.54 66.48a38.17 38.17 0 0 0 37.54-66.48z" fill="#ed261f"></path></svg>' +
      '<h3 class="yoast-modal-h3">Would you like to add keyphrase synonyms?</h3>' +
      '<p>Great news: you can, with <a class="yoast-modal-link" href="https://yoast.com/typo3-extensions-seo" target="_blank">Yoast SEO Premium for TYPO3</a></p>' +
      '<p>Other benefits of Yoast SEO Premium for you:</p>' +
      '<ul role="list"><li><strong>Rank better with synonyms &amp; related keyphrases</strong></li><li><strong>No more dead links</strong>: easy redirect manager</li><li><strong>Super fast internal links suggestions</strong></li><li><strong>24/7 support</strong></li><li><strong>No ads!</strong></li></ul>' +
      '<p><a target="_blank" href="https://yoast.com/eu/cart/?add-to-cart=80333" class="yoast-button yoast-button--noarrow yoast-button--extension yoast-button--extension-cta">Get Yoast SEO Premium for TYPO3 &raquo;</a>' +
      '<a target="_blank" href="https://yoa.st/typo3-premium-extension" class="yoast-button yoast-button--noarrow yoast-button--extension yoast-button--extension-cta yoast-button--extension-cta--white"> More information &raquo;</a></p>' +
      '<small>1 year free updates and upgrades included!</small>';
    Modal.show('Get Yoast SEO Premium for TYPO3', $(content));
  });

  $('*[data-yoast-modal-type="related-keyphrases"]').on('click', function (e) {
    e.preventDefault();
    let content = styling +
      '<svg class="yoast-modal-logo" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 488.22"><path d="M436.82 4.06A90 90 0 0 0 410 0H90A90 90 0 0 0 0 90v270a90 90 0 0 0 90 90h410V90a90 90 0 0 0-63.18-85.94z" fill="#a4286a"></path><path d="M436.82 4.06L184.15 450H500V90a90 90 0 0 0-63.18-85.94z" fill="#6c2548"></path><path d="M74.4 339.22v34.93c21.63-.85 38.51-8 52.84-22.46 14.76-14.83 27.45-38 39.93-72.85l92.53-248H215l-74.6 207.07-37.09-116.15h-41l54.42 139.79a57.49 57.49 0 0 1 0 41.84c-5.52 14.2-15.35 30.88-42.33 35.83z" fill="#fff"></path><circle cx="368.33" cy="124.68" r="97.34" transform="rotate(-45 368.335 124.68)" fill="#9fda4f"></circle><path d="M416.2 39.93l-95.74 169.51A97.34 97.34 0 1 0 416.2 39.93z" fill="#77b227"></path><path d="M294.78 254.75l-.15-.08-.13-.07a63.6 63.6 0 0 0-62.56 110.76h.13a63.6 63.6 0 0 0 62.71-110.67z" fill="#fec228"></path><path d="M294.5 254.59l-62.56 110.76a63.6 63.6 0 1 0 62.56-110.76z" fill="#f49a00"></path><path d="M222.31 450.07A38.16 38.16 0 0 0 203 416.83a38.18 38.18 0 1 0 19.41 33.27z" fill="#ff4e47"></path><path d="M202.9 416.8l-37.54 66.48a38.17 38.17 0 0 0 37.54-66.48z" fill="#ed261f"></path></svg>' +
      '<h3 class="yoast-modal-h3">Would you like to add a related keyphrase?</h3>' +
      '<p>Great news: you can, with <a class="yoast-modal-link" href="https://yoast.com/typo3-extensions-seo" target="_blank">Yoast SEO Premium for TYPO3</a></p>' +
      '<p>Other benefits of Yoast SEO Premium for you:</p>' +
      '<ul role="list"><li><strong>No more dead links</strong>: easy redirect manager</li><li><strong>Super fast internal links suggestions</strong></li><li><strong>24/7 support</strong></li><li><strong>No ads!</strong></li></ul>' +
      '<p><a href="https://yoast.com/typo3-extensions-seo" class="btn btn-default" target="_blank">Get Yoast SEO Premium for TYPO3</a></p>' +
      '<small>1 year free updates and upgrades included!</small>';
    Modal.show('Get Yoast SEO Premium for TYPO3', $(content));
  });

  $('*[data-yoast-modal-type="internal-linking-suggestion"]').on('click', function (e) {
    e.preventDefault();
    let content = styling +
      '<svg class="yoast-modal-logo" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 488.22"><path d="M436.82 4.06A90 90 0 0 0 410 0H90A90 90 0 0 0 0 90v270a90 90 0 0 0 90 90h410V90a90 90 0 0 0-63.18-85.94z" fill="#a4286a"></path><path d="M436.82 4.06L184.15 450H500V90a90 90 0 0 0-63.18-85.94z" fill="#6c2548"></path><path d="M74.4 339.22v34.93c21.63-.85 38.51-8 52.84-22.46 14.76-14.83 27.45-38 39.93-72.85l92.53-248H215l-74.6 207.07-37.09-116.15h-41l54.42 139.79a57.49 57.49 0 0 1 0 41.84c-5.52 14.2-15.35 30.88-42.33 35.83z" fill="#fff"></path><circle cx="368.33" cy="124.68" r="97.34" transform="rotate(-45 368.335 124.68)" fill="#9fda4f"></circle><path d="M416.2 39.93l-95.74 169.51A97.34 97.34 0 1 0 416.2 39.93z" fill="#77b227"></path><path d="M294.78 254.75l-.15-.08-.13-.07a63.6 63.6 0 0 0-62.56 110.76h.13a63.6 63.6 0 0 0 62.71-110.67z" fill="#fec228"></path><path d="M294.5 254.59l-62.56 110.76a63.6 63.6 0 1 0 62.56-110.76z" fill="#f49a00"></path><path d="M222.31 450.07A38.16 38.16 0 0 0 203 416.83a38.18 38.18 0 1 0 19.41 33.27z" fill="#ff4e47"></path><path d="M202.9 416.8l-37.54 66.48a38.17 38.17 0 0 0 37.54-66.48z" fill="#ed261f"></path></svg>' +
      '<h3 class="yoast-modal-h3">Would you like to get internal linking suggestions?</h3>' +
      '<p>Great news: you can, with <a class="yoast-modal-link" href="https://yoast.com/typo3-extensions-seo" target="_blank">Yoast SEO Premium for TYPO3</a></p>' +
      '<p>Other benefits of Yoast SEO Premium for you:</p>' +
      '<ul role="list"><li><strong>Rank better with synonyms &amp; related keyphrases</strong></li><li><strong>No more dead links</strong>: easy redirect manager</li><li><strong>24/7 support</strong></li><li><strong>No ads!</strong></li></ul>' +
      '<p><a href="https://yoast.com/typo3-extensions-seo" class="btn btn-default" target="_blank">Get Yoast SEO Premium for TYPO3</a></p>' +
      '<small>1 year free updates and upgrades included!</small>';
    Modal.show('Get Yoast SEO Premium for TYPO3', $(content));
  });

  $('.yoast-seo-score-bar--analysis').on('click', function (e) {
    e.preventDefault();

    let title = '';
    let content = '';
    let styling = '.modal-body li button:disabled, h4 button svg { display: none; } h4 button { cursor: inherit !important; font-size: 12px !important; background-color: inherit !important; font-weight: bold !important; margin: 0px !important; padding: 0px !important; } h4 button span { font-size: 12px; font-weight: bold; } li a { text-decoration: underline; }';

    styling += '.modal-body button { -webkit-box-pack: start; justify-content: flex-start; box-shadow: none; font-weight: normal; padding: 16px; border-width: initial; border-style: none;\n' +
        '    border-color: initial;\n' +
        '    border-image: initial;\n' +
        '    border-radius: 0px;}';
    styling += '.modal-body svg.yoast-svg-icon-circle { width: 13px; height: 13px; flex: 0 0 auto; margin: 3px 11px 0px 0px;}';
    styling += '.modal-body ul { margin: 8px 0px; padding: 0px; list-style: none;}';
    styling += '.modal-body li { min-height: 24px; display: flex; align-items: flex-start; padding: 0px;}';
    styling += '.modal-body li p { margin: 0px 8px 0px 0px; flex: 1 1 auto;}';

    if ($(this).data('yoast-analysis-type') === 'readability') {
      title = 'Readability';
      content = $('#YoastPageHeaderAnalysisReadability').html();
    }

    if ($(this).data('yoast-analysis-type') === 'seo') {
      title = 'SEO';
      if (typeof YoastConfig.focusKeyphrase.keyword !== "undefined" && YoastConfig.focusKeyphrase.keyword !== null && YoastConfig.focusKeyphrase.keyword !== '') {
        title += ': ' + YoastConfig.focusKeyphrase.keyword;
      }
      content = $('#YoastPageHeaderAnalysisSeo').html();
    }

    if (title && content) {
      content = '<style>' + styling + '</style>' + content;

      Modal.show(title, $(content));
    }
  });

});
