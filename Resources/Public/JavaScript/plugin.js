import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import { debounce} from 'lodash';

import SnippetPreview from './Components/SnippetPreview';
import Analysis from './Components/Analysis';
import TitleProgressBar from './Components/TitleProgressBar';
import DescriptionProgressBar from './Components/DescriptionProgressBar';
import store from './redux/store';
import {getContent, updateContent} from './redux/actions/content';
import {setFocusKeyword} from './redux/actions/focusKeyword';
import RelevantWords from "./Components/RelevantWords";
import {saveRelevantWords} from './redux/actions/relevantWords';

import createAnalysisWorker from './analysis/createAnalysisWorker';
import refreshAnalysis from './analysis/refreshAnalysis';
import {setFocusKeywordSynonyms} from "./redux/actions/focusKeywordSynonyms";

const prominentWordsSaveUrl = '/?type=1539541406';

let worker = createAnalysisWorker(YoastConfig.isCornerstoneContent);

store.dispatch(setFocusKeyword(YoastConfig.focusKeyphrase.keyword));
store.dispatch(setFocusKeywordSynonyms(YoastConfig.focusKeyphrase.synonyms));

store
    .dispatch(getContent())
    .then(_ => refreshAnalysis(worker, store))
    .then(_ => {
        document.querySelectorAll('[data-yoast-analysis]').forEach(container => {
            const config = {};
            config.resultType = container.getAttribute('data-yoast-analysis');

            if (container.getAttribute('data-yoast-subtype')) {
                config.resultSubtype = container.getAttribute('data-yoast-subtype');
            }

            ReactDOM.render(<Provider store={store}><Analysis {...config} /></Provider>, container);
        });

        store.dispatch(saveRelevantWords(store.getState().relevantWords, tx_yoast_seo.settings.vanillaUid, tx_yoast_seo.settings.languageId, prominentWordsSaveUrl));
    });

document.querySelectorAll('[data-yoast-snippetpreview]').forEach(container => {
    ReactDOM.render(<Provider store={store}><SnippetPreview /></Provider>, container);
});

document.querySelectorAll('[data-yoast-insights]').forEach(container => {
    ReactDOM.render(<Provider store={store}><RelevantWords /></Provider>, container);
});

if (typeof $cornerstoneFieldSelector !== 'undefined') {
    document.querySelector('[data-formengine-input-name="' + $cornerstoneFieldSelector + '"]').addEventListener('change', function() {
        worker = createAnalysisWorker(this.checked);
        refreshAnalysis(worker, store);
    });
}

const progressBarItems = [{
    input: document.querySelector(`[data-formengine-input-name="${$titleTcaSelector}"]`),
    component: <TitleProgressBar />,
    storeKey: 'title'
}, {
    input: document.querySelector(`[data-formengine-input-name="${$descriptionTcaSelector}"]`),
    component: <DescriptionProgressBar />,
    storeKey: 'description'
}]

progressBarItems.forEach(item => {
    if (item.input) {
        const container = document.createElement('div');
        item.input.after(container);

        item.input.addEventListener('input', debounce(_ => {
            store.dispatch(updateContent({[item.storeKey]: item.input.value}));
        }, 100));

        item.input.addEventListener('change', _ => {
            refreshAnalysis(worker, store);
        })

        ReactDOM.render(<Provider store={store}>{item.component}</Provider>, container);
    }
})