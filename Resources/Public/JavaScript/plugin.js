import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import SnippetPreview from './Components/SnippetPreview';
import Analysis from './Components/Analysis';
import store from './redux/store';
import {getContent} from './redux/actions/content';
import {setFocusKeyword} from './redux/actions/focusKeyword';
import {setCornerstoneContent} from './redux/actions/cornerstoneContent';
import RelevantWords from "./Components/RelevantWords";

import worker from './analysis/worker';
import refreshAnalysis from './analysis/refreshAnalysis';

const keyword = tx_yoast_seo.settings.focusKeyword;
const useCornerstone = tx_yoast_seo.settings.cornerstone;

store.dispatch(setFocusKeyword(keyword));

store
    .dispatch(getContent(keyword))
    .then(_ => refreshAnalysis(worker, store))
    .then(_ => {
        document.querySelectorAll('[data-yoast-analysis]').forEach(container => {
            const config = {};
            config.resultType = container.getAttribute('data-yoast-analysis');

            if (config.resultType === 'seo') {
                config.resultSubtype = '';

                if (container.closest('[id*="tx_yoastseo_focuskeyword_premium"]')) {
                    config.resultSubtype = 'een';
                }
            }

            ReactDOM.render(<Provider store={store}><Analysis {...config} /></Provider>, container);
        });
    });

document.querySelectorAll('[data-yoast-snippetpreview]').forEach(container => {
    ReactDOM.render(<Provider store={store}><SnippetPreview /></Provider>, container);
});

document.querySelectorAll('[data-yoast-insights]').forEach(container => {
    ReactDOM.render(<Provider store={store}><RelevantWords /></Provider>, container);
});

if (typeof $cornerstoneFieldSelector !== 'undefined') {
    document.querySelector('[data-formengine-input-name="' + $cornerstoneFieldSelector + '"]').addEventListener('change', function() {
        store.dispatch(setCornerstoneContent(this.checked, workerUrl));
    });
}
