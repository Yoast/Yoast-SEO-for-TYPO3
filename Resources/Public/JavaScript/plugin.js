import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import SnippetPreview from './Components/SnippetPreview';
import ReadabilityAnalysis from './Components/ReadabilityAnalysis';
import SeoAnalysis from './Components/SeoAnalysis';
import store from './redux/store';
import {getContent} from './redux/actions/content';
import {setFocusKeyword} from './redux/actions/focusKeyword';
import {setCornerstoneContent} from './redux/actions/cornerstoneContent';
import RelevantWords from "./Components/RelevantWords";

const keyword = tx_yoast_seo.settings.focusKeyword;
const synonyms = '';
const useCornerstone = tx_yoast_seo.settings.cornerstone;

store.dispatch(getContent(keyword, synonyms, useCornerstone));
store.dispatch(setFocusKeyword(keyword));

document.querySelectorAll('[data-yoast-snippetpreview]').forEach(container => {
    ReactDOM.render(<Provider store={store}><SnippetPreview /></Provider>, container);
});

document.querySelectorAll('[data-yoast-readability-analysis]').forEach(container => {
    ReactDOM.render(<Provider store={store}><ReadabilityAnalysis /></Provider>, container);
});

document.querySelectorAll('[data-yoast-seo-analysis]').forEach(container => {
    ReactDOM.render(<Provider store={store}><SeoAnalysis /></Provider>, container);
});

document.querySelectorAll('[data-yoast-insights]').forEach(container => {
    ReactDOM.render(<Provider store={store}><RelevantWords /></Provider>, container);
});

    if (typeof $cornerstoneFieldSelector !== 'undefined') {
        document.querySelector('[data-formengine-input-name="' + $cornerstoneFieldSelector + '"]').addEventListener('change', function() {
            store.dispatch(setCornerstoneContent(this.checked));
        });
    }
