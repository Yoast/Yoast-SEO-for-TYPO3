import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import { debounce} from 'lodash';

import SnippetPreview from './Components/SnippetPreview';
import Analysis from './Components/Analysis';
import StatusIcon from './Components/StatusIcon';
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

let worker = createAnalysisWorker(YoastConfig.isCornerstoneContent, 'en_US');

store.dispatch(setFocusKeyword(YoastConfig.focusKeyphrase.keyword));
store.dispatch(setFocusKeywordSynonyms(YoastConfig.focusKeyphrase.synonyms));

store
    .dispatch(getContent())
    .then(_ => {
        worker = createAnalysisWorker(YoastConfig.isCornerstoneContent, store.getState().content.locale);
        return refreshAnalysis(worker, store)
    })
    .then(_ => {
        document.querySelectorAll('[data-yoast-analysis]').forEach(container => {
            const config = {};
            config.resultType = container.getAttribute('data-yoast-analysis');

            if (config.resultType === 'seo') {
                config.resultSubtype = container.getAttribute('data-yoast-subtype');
            }

            ReactDOM.render(<Provider store={store}><Analysis {...config} /></Provider>, container);
        });

        if (typeof YoastConfig.useRelevantWords !== 'undefined' &&
             YoastConfig.useRelevantWords === true)
        {
            store.dispatch(saveRelevantWords(store.getState().relevantWords, YoastConfig.data.uid, YoastConfig.data.languageId, YoastConfig.data.table, YoastConfig.urls.prominentWords));
        }
    });

document.querySelectorAll('[data-yoast-analysis]').forEach(container => {
    const config = {};
    config.resultType = container.getAttribute('data-yoast-analysis');

    let formContainer = container.closest('.form-section');

    if (formContainer !== null) {
        let titleContainer = formContainer.querySelector('h4');
        let iconContainer = document.createElement('span');

        if (typeof titleContainer !== "undefined" && titleContainer !== null) {
            iconContainer.classList.add('yoast-seo-status-icon');
            titleContainer.prepend(iconContainer);
        } else {
            titleContainer = container.closest('.panel').querySelector('.t3js-icon');
            iconContainer.style.cssText = 'top: 3px; position: relative;';
            titleContainer.replaceWith(iconContainer);
        }

        if (config.resultType === 'seo') {
            config.resultSubtype = container.getAttribute('data-yoast-subtype');
        }

        ReactDOM.render(<Provider store={store}><StatusIcon {...config} text="false" /></Provider>, iconContainer);
    }
});

document.querySelectorAll('.yoast-seo-score-bar--analysis').forEach(container => {
    let type = container.getAttribute('data-yoast-analysis-type');
    const configReadability = {};
    const configSeo = {};

    configReadability.resultType = 'readability';
    configSeo.resultType = 'seo';
    configSeo.resultSubtype = '';

    if (type === 'readability') {
        ReactDOM.render(<Provider store={store}><StatusIcon {...configReadability} text="true" /></Provider>, container);
    }

    if (type === 'seo') {
        ReactDOM.render(<Provider store={store}><StatusIcon {...configSeo} text="true" /></Provider>, container);
    }
});

document.querySelectorAll('[data-yoast-snippetpreview]').forEach(container => {
    ReactDOM.render(<Provider store={store}><SnippetPreview /></Provider>, container);
});

document.querySelectorAll('[data-yoast-insights]').forEach(container => {
    ReactDOM.render(<Provider store={store}><RelevantWords /></Provider>, container);
});

if (typeof YoastConfig.fieldSelectors !== 'undefined' &&
    typeof YoastConfig.fieldSelectors.cornerstone !== 'undefined')
{
    document.querySelector(`[data-formengine-input-name="${YoastConfig.fieldSelectors.cornerstone}"]`).addEventListener('change', function() {
        worker = createAnalysisWorker(this.checked);
        refreshAnalysis(worker, store);
    });
}

if (typeof YoastConfig.fieldSelectors !== 'undefined' &&
    typeof YoastConfig.fieldSelectors.title !== 'undefined' &&
    typeof YoastConfig.fieldSelectors.description !== 'undefined')
{
    const progressBarItems = [{
        input: document.querySelector(`[data-formengine-input-name="${YoastConfig.fieldSelectors.title}"]`),
        component: <TitleProgressBar/>,
        storeKey: 'title'
    }, {
        input: document.querySelector(`[data-formengine-input-name="${YoastConfig.fieldSelectors.description}"]`),
        component: <DescriptionProgressBar/>,
        storeKey: 'description'
    }];

    progressBarItems.forEach(item => {
        if (item.input) {
            const container = document.createElement('div');
            item.input.after(container);
            item.input.addEventListener('input', debounce(_ => {
                let value = item.input.value;

                if (item.storeKey === 'title') {
                    if (typeof YoastConfig.pageTitlePrepend !== "undefined" &&
                        YoastConfig.pageTitlePrepend !== null &&
                        YoastConfig.pageTitlePrepend !== ''
                    )
                    {
                        value = YoastConfig.pageTitlePrepend + value;
                    }

                    if (typeof YoastConfig.pageTitleAppend !== "undefined" &&
                        YoastConfig.pageTitleAppend !== null &&
                        YoastConfig.pageTitleAppend !== '')
                    {
                        value = value + YoastConfig.pageTitleAppend;
                    }

                }

                store.dispatch(updateContent({[item.storeKey]: value}));
            }, 100));

            item.input.addEventListener('change', _ => {
                refreshAnalysis(worker, store);
            });

            ReactDOM.render(<Provider store={store}>{item.component}</Provider>, container);
        }
    });
}

if (typeof YoastConfig.fieldSelectors !== 'undefined' &&
    typeof YoastConfig.fieldSelectors.focusKeyword !== 'undefined')
{
    document.querySelectorAll(`[data-formengine-input-name="${YoastConfig.fieldSelectors.focusKeyword}"]`).forEach(item => {
        item.addEventListener('input', debounce(_ => {
            store.dispatch(setFocusKeyword(item.value));
            refreshAnalysis(worker, store);
        }, 500));
    });

}

if (typeof YoastConfig.fieldSelectors !== 'undefined' &&
    typeof YoastConfig.fieldSelectors.focusKeywordSynonyms !== 'undefined')
{
    document.querySelectorAll(`[data-formengine-input-name="${YoastConfig.fieldSelectors.focusKeywordSynonyms}"]`).forEach(item => {
        item.addEventListener('input', debounce(_ => {
            store.dispatch(setFocusKeywordSynonyms(item.value));
            refreshAnalysis(worker, store);
        }, 500));
    });

}

if (typeof YoastConfig.fieldSelectors !== 'undefined' &&
    typeof YoastConfig.fieldSelectors.premiumKeyword !== 'undefined' &&
    YoastConfig.fieldSelectors.premiumKeyword !== ''
) {
    document.querySelector(`#${YoastConfig.fieldSelectors.premiumKeyword}`).querySelectorAll(`.panel-heading`).forEach(item => {
        item.addEventListener('click', debounce(_ => {
            document.querySelectorAll('[data-yoast-analysis]').forEach(container => {
                const config = {};
                config.resultType = container.getAttribute('data-yoast-analysis');

                if (config.resultType === 'seo') {
                    config.resultSubtype = container.getAttribute('data-yoast-subtype');
                }

                ReactDOM.render(<Provider store={store}><Analysis {...config}/></Provider>, container);
            });
        }, 2000));
    });
}

if (typeof YoastConfig.fieldSelectors !== 'undefined' &&
    typeof YoastConfig.fieldSelectors.title !== 'undefined' &&
    YoastConfig.fieldSelectors.title !== ''
) {
    document.querySelectorAll(`li.t3js-tabmenu-item`).forEach(item => {
       if (item.innerHTML.includes('Yoast SEO')) {
           item.addEventListener('click', debounce(_ => {
               let element = document.querySelector(`[data-formengine-input-name="${YoastConfig.fieldSelectors.title}"]`);
               let value = element.value;

               if (typeof YoastConfig.pageTitlePrepend !== "undefined" &&
                   YoastConfig.pageTitlePrepend !== null &&
                   YoastConfig.pageTitlePrepend !== ''
               )
               {
                   value = YoastConfig.pageTitlePrepend + value;
               }

               if (typeof YoastConfig.pageTitleAppend !== "undefined" &&
                   YoastConfig.pageTitleAppend !== null &&
                   YoastConfig.pageTitleAppend !== '')
               {
                   value = value + YoastConfig.pageTitleAppend;
               }

               store.dispatch(updateContent({title: value + ' '}));
           }, 100));
       }
    });
}
