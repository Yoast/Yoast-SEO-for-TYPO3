import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import SnippetPreview from './Components/SnippetPreview';
import ReadabilityAnalysis from './Components/ReadabilityAnalysis';
import store from './redux/store';
import {getContent} from './redux/actions/content';
import {setFocusKeyword} from './redux/actions/focusKeyword';

const keyword = tx_yoast_seo.settings.focusKeyword;

store.dispatch(getContent(keyword));
store.dispatch(setFocusKeyword(keyword));

document.querySelectorAll('[data-yoast-snippetpreview]').forEach(container => {
    ReactDOM.render(<Provider store={store}><SnippetPreview /></Provider>, container);
});

document.querySelectorAll('[data-yoast-readability-analysis]').forEach(container => {
    ReactDOM.render(<Provider store={store}><ReadabilityAnalysis /></Provider>, container);
});
