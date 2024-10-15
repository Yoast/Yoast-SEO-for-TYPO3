import $ from 'jquery';

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