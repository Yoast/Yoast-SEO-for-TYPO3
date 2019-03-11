import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import SnippetPreview from './Components/SnippetPreview';
import Analysis from './Components/Analysis';
import store from './redux/store';
import {getContent} from './redux/actions/content';
import {setFocusKeyword} from './redux/actions/focusKeyword';
import {analyzeData} from './redux/actions/analysis';

const keyword = tx_yoast_seo.settings.focusKeyword;

const workerUrl = '/typo3conf/ext/yoast_seo/Resources/Public/JavaScript/dist/worker.js';

store.dispatch(setFocusKeyword(keyword));

store
    .dispatch(getContent(keyword))
    .then(data => {
        return store.dispatch(
            analyzeData(store.getState().content, keyword, 'bla', workerUrl)
        );
    })
    .then(_ => {
        document.querySelectorAll('[data-yoast-analysis]').forEach(container => {
            const config = {};
            config.resultType = container.getAttribute('data-yoast-analysis');

            if (config.resultType === 'seo') {
                config.resultSubtype = '';
            }

            ReactDOM.render(<Provider store={store}><Analysis {...config} /></Provider>, container);
        });
    })

document.querySelectorAll('[data-yoast-snippetpreview]').forEach(container => {
    ReactDOM.render(<Provider store={store}><SnippetPreview /></Provider>, container);
});
