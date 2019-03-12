import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import SnippetPreview from './Components/SnippetPreview';
import Analysis from './Components/Analysis';
import store from './redux/store';
import {getContent} from './redux/actions/content';
import {setFocusKeyword} from './redux/actions/focusKeyword';
import {analyzeData} from './redux/actions/analysis';
import {setCornerstoneContent} from './redux/actions/cornerstoneContent';
import RelevantWords from "./Components/RelevantWords";
import {getRelevantWords} from './redux/actions/relevantWords';
import {saveRelevantWords} from './redux/actions/relevantWords';

const keyword = tx_yoast_seo.settings.focusKeyword;
const synonyms = '';
const useCornerstone = tx_yoast_seo.settings.cornerstone;

const workerUrl = '/typo3conf/ext/yoast_seo/Resources/Public/JavaScript/dist/worker.js';
const prominentWordsSaveUrl = '/?type=1539541406';

store.dispatch(setFocusKeyword(keyword));

store
    .dispatch(getContent(keyword))
    .then(data => {
        return Promise.all([
            store.dispatch(analyzeData(store.getState().content, keyword, synonyms, workerUrl, useCornerstone)),
            store.dispatch(getRelevantWords(store.getState().content, keyword, synonyms, workerUrl, useCornerstone))
        ]);
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

        store.dispatch(saveRelevantWords(store.getState().relevantWords, tx_yoast_seo.settings.vanillaUid, tx_yoast_seo.settings.languageId, prominentWordsSaveUrl));
    })

document.querySelectorAll('[data-yoast-snippetpreview]').forEach(container => {
    ReactDOM.render(<Provider store={store}><SnippetPreview /></Provider>, container);
});

document.querySelectorAll('[data-yoast-insights]').forEach(container => {
    ReactDOM.render(<Provider store={store}><RelevantWords /></Provider>, container);
});

if (typeof $cornerstoneFieldSelector !== 'undefined') {
    document.querySelector('[data-formengine-input-name="' + $cornerstoneFieldSelector + '"]').addEventListener('change', function() {
        Promise.all([
            store.dispatch(setCornerstoneContent(this.checked, workerUrl))
        ]).then(_ => {
            let state = store.getState();

            store.dispatch(analyzeData(state.content, state.focusKeyword, synonyms, workerUrl, state.cornerstoneContent))
        });
    });
}
