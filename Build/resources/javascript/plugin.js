import React from 'react';
import {createRoot} from 'react-dom/client';
import {Provider} from 'react-redux';
import {debounce} from 'lodash';

import SnippetPreview from './Components/SnippetPreview';
import Analysis from './Components/Analysis';
import StatusIcon from './Components/StatusIcon';
import TitleProgressBar from './Components/TitleProgressBar';
import DescriptionProgressBar from './Components/DescriptionProgressBar';
import LinkingSuggestions from "./Components/LinkingSuggestions";
import Insights from "./Components/Insights";
import store from './redux/store';
import {getContent, updateContent} from './redux/actions/content';
import {setFocusKeyword} from './redux/actions/focusKeyword';

import {saveRelevantWords} from './redux/actions/relevantWords';
import createAnalysisWorker from './analysis/createAnalysisWorker';
import refreshAnalysis from './analysis/refreshAnalysis';
import {setFocusKeywordSynonyms} from "./redux/actions/focusKeywordSynonyms";
import {setLocaleData} from "@wordpress/i18n";
import {Paper} from "yoastseo";
import {getLinkingSuggestions} from "./redux/actions/linkingSuggestions";

let YoastTypo3 = {
    _yoastWorker: null,

    _progressBarInitialized: false,

    init: function () {
        if (typeof YoastConfig === 'undefined') {
            return;
        }
        if (typeof YoastConfig.data !== 'undefined' && typeof YoastConfig.data.uid !== 'undefined') {
            YoastPlugin.init();
        }
        if (typeof YoastConfig.urls.linkingSuggestions !== 'undefined') {
            YoastLinkingSuggestions.init(YoastConfig.urls.linkingSuggestions);
        }
        if (typeof YoastConfig.urls.indexPages !== 'undefined') {
            YoastCrawler.init();
        }
    },

    setWorker: function (cornerstone, locale = 'en_US') {
        if (YoastTypo3._yoastWorker !== null) {
            YoastTypo3._yoastWorker._worker.terminate();
        }
        YoastTypo3._yoastWorker = createAnalysisWorker(cornerstone, locale);
    },

    setLocales: function () {
        setLocaleData({'': {'yoast-components': {}}}, 'yoast-components');
        if (YoastConfig.translations) {
            for (let translation of YoastConfig.translations) {
                setLocaleData(translation.locale_data['wordpress-seo'], translation.domain);
            }
        } else {
            setLocaleData({'': {'wordpress-seo': {}}}, 'wordpress-seo');
        }
    },

    postRequest: (url, data) => {
        return fetch(url, {
            method: 'POST',
            cache: 'no-cache',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
    }
}

let YoastPlugin = {
    init: () => {
        YoastTypo3.setWorker(YoastConfig.isCornerstoneContent);
        YoastTypo3.setLocales();

        store.dispatch(setFocusKeyword(YoastConfig.focusKeyphrase.keyword));
        store.dispatch(setFocusKeywordSynonyms(YoastConfig.focusKeyphrase.synonyms));

        YoastPlugin.initContentAnalysis();
        YoastPlugin.initStatusIcon();
        YoastPlugin.initScoreBars();
        YoastPlugin.initTCA();
        YoastPlugin.initSnippetPreviewAndInsights();
        YoastPlugin.initCornerstone();
        YoastPlugin.initProgressBars();
        YoastPlugin.initFocusKeyword();
        YoastPlugin.initRelatedFocusKeywords();
        YoastPlugin.initSeoTitle();
    },

    initContentAnalysis: () => {
        store
            .dispatch(getContent())
            .then(_ => {
                YoastTypo3.setWorker(YoastConfig.isCornerstoneContent, store.getState().content.locale);
                return YoastPlugin.refreshAnalysis();
            })
            .then(_ => {
                document.querySelectorAll('[data-yoast-analysis]').forEach(container => {
                    const config = {};
                    config.resultType = container.getAttribute('data-yoast-analysis');

                    if (config.resultType === 'seo') {
                        config.resultSubtype = container.getAttribute('data-yoast-subtype');
                    }

                    const root = createRoot(container);
                    root.render(<Provider store={store}><Analysis {...config} /></Provider>);
                });
                store.dispatch(saveRelevantWords(store.getState().relevantWords, YoastConfig.data.uid, YoastConfig.data.pid, YoastConfig.data.languageId, YoastConfig.data.table, YoastConfig.urls.prominentWords));
            });
    },

    initStatusIcon: () => {
        document.querySelectorAll('[data-yoast-analysis]').forEach(container => {
            const config = {};
            config.resultType = container.getAttribute('data-yoast-analysis');

            let formContainer = container.closest('.form-section');
            if (formContainer === null) {
                return;
            }

            let titleContainer = formContainer.querySelector('h4');
            let iconContainer = document.createElement('span');

            if (typeof titleContainer !== "undefined" && titleContainer !== null) {
                iconContainer.classList.add('yoast-seo-status-icon');
                titleContainer.prepend(iconContainer);
            } else {
                let panel = container.closest('.panel');

                if (panel !== null) {
                    titleContainer = panel.querySelector('.t3js-icon');
                    iconContainer.style.cssText = 'top: 3px; position: relative;';
                    titleContainer.replaceWith(iconContainer);
                }
            }

            if (config.resultType === 'seo') {
                config.resultSubtype = container.getAttribute('data-yoast-subtype');
            }

            const iconRoot = createRoot(iconContainer);
            iconRoot.render(<Provider store={store}><StatusIcon {...config} text="false" /></Provider>);
        });
    },

    initScoreBars: () => {
        document.querySelectorAll('.yoast-seo-score-bar--analysis').forEach(container => {
            let type = container.getAttribute('data-yoast-analysis-type');
            const configReadability = {};
            const configSeo = {};

            configReadability.resultType = 'readability';
            configSeo.resultType = 'seo';
            configSeo.resultSubtype = '';

            const seoScoreContainer = createRoot(container);

            if (type === 'readability') {
                seoScoreContainer.render(<Provider store={store}><StatusIcon {...configReadability}
                                                                    text="true" /></Provider>);
            }

            if (type === 'seo') {
                seoScoreContainer.render(<Provider store={store}><StatusIcon {...configSeo}
                                                                    text="true" /></Provider>);
            }
        });
    },

    initTCA: () => {
        if (typeof YoastConfig.TCA === 'undefined' || YoastConfig.TCA !== 1) {
            return;
        }

        document.querySelectorAll('h1').forEach(container => {
            const configReadability = {};
            const configSeo = {};

            configReadability.resultType = 'readability';
            configSeo.resultType = 'seo';
            configSeo.resultSubtype = '';

            let scoreBar = document.createElement('div');
            scoreBar.classList.add('yoast-seo-score-bar');

            // Readability
            let readabilityContainer = document.createElement('span');
            readabilityContainer.classList.add('yoast-seo-score-bar--analysis');
            readabilityContainer.classList.add('yoast-seo-tca');
            scoreBar.append(readabilityContainer);

            // Seo
            let seoContainer = document.createElement('span');
            seoContainer.classList.add('yoast-seo-score-bar--analysis');
            seoContainer.classList.add('yoast-seo-tca');
            scoreBar.append(seoContainer);

            container.parentNode.insertBefore(scoreBar, container.nextSibling);

            const readabilityRoot = createRoot(readabilityContainer);
            readabilityRoot.render(<Provider store={store}><StatusIcon {...configReadability}
                                                                text="true" /></Provider>);
            const seoRoot = createRoot(seoContainer);
            seoRoot.render(<Provider store={store}><StatusIcon {...configSeo} text="true" /></Provider>);
        });
    },

    initSnippetPreviewAndInsights: () => {
        document.querySelectorAll('[data-yoast-snippetpreview]').forEach(container => {
            const snippetRoot = createRoot(container);
            snippetRoot.render(<Provider store={store}><SnippetPreview /></Provider>);
        });

        document.querySelectorAll('[data-yoast-insights]').forEach(container => {
            const insightsRoot = createRoot(container);
            insightsRoot.render(<Provider store={store}><Insights /></Provider>);
        });
    },

    initCornerstone: () => {
        let cornerStoneField = YoastPlugin.getFormEngineElement('cornerstone');
        if (cornerStoneField !== null) {
            cornerStoneField.addEventListener('change', function () {
                YoastTypo3.setWorker(this.checked);
                YoastPlugin.refreshAnalysis();
            });
        }
    },

    initProgressBars: () => {
        let titleField = YoastPlugin.getFormEngineElement('title');
        let descriptionField = YoastPlugin.getFormEngineElement('description');
        if (titleField === null || descriptionField === null) {
            return;
        }
        YoastPlugin._progressBarInitialized = true;
        const progressBarItems = [{
            input: titleField,
            component: <TitleProgressBar />,
            storeKey: 'title'
        }, {
            input: descriptionField,
            component: <DescriptionProgressBar />,
            storeKey: 'description'
        }];

        progressBarItems.forEach((item) => {
            if (item.input) {
                let container = document.createElement('div');
                YoastPlugin.addProgressContainer(item.input, container);
                item.input.addEventListener('input', debounce(_ => {
                    let value = item.input.value;

                    let pageTitleElement = YoastPlugin.getFormEngineElement('pageTitle');
                    let titleValue = pageTitleElement.value;

                    if (value === '') {
                        value = titleValue;
                    }

                    if (item.storeKey === 'title') {
                        value = YoastPlugin.getTitleValue(value);
                    }

                    store.dispatch(updateContent({[item.storeKey]: value}));
                }, 100));

                item.input.addEventListener('change', _ => {
                    YoastPlugin.refreshAnalysis();
                });

                const progressBarRoot = createRoot(container);
                progressBarRoot.render(<Provider store={store}>{item.component}</Provider>);
            }
        });
    },

    // TODO: This dirty fix is necessary because of the differences between CMS9, CMS10 and CMS11.
    // When support for CMS9 and CMS10 is dropped, this should be handled differently
    addProgressContainer: (input, container) => {
        let formWrap = input.closest('.form-wizards-wrap');
        let computedStyle = window.getComputedStyle(formWrap);
        if (computedStyle.getPropertyValue('display') === 'grid') {
            container.style.gridArea = 'bottom';
            let formControls = input.closest('.form-control-wrap');
            if (formControls.style.maxWidth) {
                container.style.maxWidth = formControls.style.maxWidth;
            }
            input.closest('.form-control-wrap').after(container);
        } else {
            input.after(container);
        }
    },

    initFocusKeyword: () => {
        YoastPlugin.getFormEngineElements('focusKeyword').forEach(item => {
            item.addEventListener('input', debounce(_ => {
                store.dispatch(setFocusKeyword(item.value));
                YoastPlugin.refreshAnalysis();
            }, 500));
        });

        YoastPlugin.getFormEngineElements('focusKeywordSynonyms').forEach(item => {
            item.addEventListener('input', debounce(_ => {
                store.dispatch(setFocusKeywordSynonyms(item.value));
                YoastPlugin.refreshAnalysis();
            }, 500));
        });
    },

    initRelatedFocusKeywords: () => {
        if (typeof YoastConfig === 'undefined'
            || typeof YoastConfig.fieldSelectors === 'undefined'
            || typeof YoastConfig.fieldSelectors.relatedKeyword === 'undefined'
        ) {
            return;
        }
        let relatedKeywordField = document.querySelector(`#${YoastConfig.fieldSelectors.relatedKeyword}`);
        if (relatedKeywordField === null) {
            return;
        }
        relatedKeywordField.querySelectorAll(`.panel-heading`).forEach(item => {
            item.addEventListener('click', debounce(_ => {
                document.querySelectorAll('[data-yoast-analysis]').forEach(container => {
                    const config = {};
                    config.resultType = container.getAttribute('data-yoast-analysis');

                    if (config.resultType === 'seo') {
                        config.resultSubtype = container.getAttribute('data-yoast-subtype');
                    }

                    const relatedRoot = createRoot(container);
                    relatedRoot.render(<Provider store={store}><Analysis {...config} /></Provider>);
                });
            }, 2000));
        });
    },

    initSeoTitle: () => {
        let titleField = YoastPlugin.getFieldSelector('title');
        let pageTitleField = YoastPlugin.getFieldSelector('pageTitle');

        if (titleField === false || pageTitleField === false) {
            return;
        }

        let pageTitleElement = YoastPlugin.getFormEngineElement('pageTitle');
        if (pageTitleElement) {
            pageTitleElement.addEventListener('change', function (event) {
                YoastPlugin.setSeoTitlePlaceholder();
            });
        }

        window.addEventListener('load', function (event) {
            YoastPlugin.setSeoTitlePlaceholder();
        });

        document.querySelectorAll(`li.t3js-tabmenu-item`).forEach(item => {
            if (item.innerHTML.includes('SEO')) {
                item.addEventListener('click', debounce(_ => {
                    if (YoastPlugin._progressBarInitialized === false) {
                        YoastPlugin.initProgressBars();
                    }
                    let element = YoastPlugin.getFormEngineElement('title');
                    let value = element.value;

                    let pageTitleElement = YoastPlugin.getFormEngineElement('pageTitle');
                    let titleValue = pageTitleElement.value;

                    element.setAttribute('placeholder', titleValue);
                    if (value === '') {
                        value = titleValue;
                    }
                    value = YoastPlugin.getTitleValue(value);

                    store.dispatch(updateContent({title: value + ' '}));
                }, 100));
            }
        });
    },

    getTitleValue: (value) => {
        if (typeof YoastConfig.pageTitlePrepend !== "undefined" &&
            YoastConfig.pageTitlePrepend !== null &&
            YoastConfig.pageTitlePrepend !== ''
        ) {
            value = YoastConfig.pageTitlePrepend + value;
        }

        if (typeof YoastConfig.pageTitleAppend !== "undefined" &&
            YoastConfig.pageTitleAppend !== null &&
            YoastConfig.pageTitleAppend !== ''
        ) {
            value = value + YoastConfig.pageTitleAppend;
        }
        return value;
    },

    setSeoTitlePlaceholder: () => {
        let seoTitleElement = YoastPlugin.getFormEngineElement('title');
        let pageTitleElement = YoastPlugin.getFormEngineElement('pageTitle');
        if (seoTitleElement && pageTitleElement) {
            let titleValue = pageTitleElement.value;
            seoTitleElement.setAttribute('placeholder', titleValue);
        }
    },

    getFieldSelector: (fieldName) => {
        if (typeof YoastConfig.fieldSelectors === 'undefined') {
            return false;
        }

        if (typeof YoastConfig.fieldSelectors[fieldName] === 'undefined') {
            return false;
        }

        return YoastConfig.fieldSelectors[fieldName];
    },

    getFormEngineElement: (name) => {
        let fieldName = YoastPlugin.getFieldSelector(name);
        if (fieldName === false) {
            return null;
        }
        return document.querySelector(`[data-formengine-input-name="${fieldName}"]`);
    },

    getFormEngineElements: (name) => {
        let fieldName = YoastPlugin.getFieldSelector(name);
        if (fieldName === false) {
            return [];
        }
        return document.querySelectorAll(`[data-formengine-input-name="${fieldName}"]`);
    },

    refreshAnalysis: () => {
        return refreshAnalysis(YoastTypo3._yoastWorker, store);
    },
}

let YoastLinkingSuggestions = {
    _url: null,
    _updateInterval: null,

    init: (url) => {
        YoastLinkingSuggestions._url = url;
        YoastTypo3.setWorker(false, YoastConfig.linkingSuggestions.locale);

        document.querySelectorAll('[data-yoast-linking-suggestions]').forEach(container => {
            const linkingSuggestionsRoot = createRoot(container);
            linkingSuggestionsRoot.render(<Provider store={store}><LinkingSuggestions /></Provider>);
        });

        YoastLinkingSuggestions._updateInterval = setInterval(() => {
            YoastLinkingSuggestions.checkLinkingSuggestions();
        }, 1000);
    },

    checkLinkingSuggestions: () => {
        let content = YoastLinkingSuggestions.getCKEditorContent();
        if (content === null) {
            return;
        }

        store.dispatch(getLinkingSuggestions(YoastTypo3._yoastWorker, content, YoastLinkingSuggestions._url));

        // Analysis is done, Updating every second is not necessary anymore
        clearInterval(YoastLinkingSuggestions._updateInterval);
        YoastLinkingSuggestions._updateInterval = setInterval(() => {
            YoastLinkingSuggestions.checkLinkingSuggestions();
        }, 10000);
    },

    getCKEditorContent: () => {
        const ckeditor5elements = document.getElementsByTagName('typo3-rte-ckeditor-ckeditor5');
        if (ckeditor5elements.length > 0) {
            const editableElements = document.querySelectorAll('.ck-editor__editable');
            let content = '';
            let ckeditorLoaded = false;
            for (let editorElement in editableElements) {
                if (typeof editableElements[editorElement].ckeditorInstance !== 'undefined') {
                    ckeditorLoaded = true;
                    content += editableElements[editorElement].ckeditorInstance.getData();
                }
            }
            if (ckeditorLoaded === false) {
                return null;
            }
            return content;
        } else {
            if (typeof CKEDITOR === 'undefined') {
                return null;
            }
            let content = '';
            for (let instance in CKEDITOR.instances) {
                if (CKEDITOR.instances.hasOwnProperty(instance)) {
                    content += CKEDITOR.instances[instance].getData();
                }
            }
            return content;
        }
    },
}

let YoastCrawler = {
    _progressCache: [],

    init: () => {
        let buttons = document.querySelectorAll('.js-crawler-index');
        buttons.forEach((button) => {
            button.addEventListener('click', (e) => {
                YoastCrawler.startIndex(button.dataset.site, button.dataset.language);
            });
        });
    },

    startIndex: (site, language) => {
        YoastCrawler.setButtonsDisableStatus(true);

        document.querySelector('#saved-progress-' + site + '-' + language).remove();
        let progressDiv = document.querySelector('#progress-' + site + '-' + language);
        progressDiv.classList.remove('hide');

        let pagesAmountProgressDiv = progressDiv.querySelector('.js-crawler-pages-progress');
        let pagesAmountSuccessDiv = progressDiv.querySelector('.js-crawler-pages-success');
        YoastTypo3.postRequest(YoastConfig.urls.determinePages, {
            site: site,
            language: language
        })
            .then((response) => response.json())
            .then((response) => {
                if (typeof response.error !== 'undefined') {
                    pagesAmountProgressDiv.innerHTML = response.error;
                    pagesAmountProgressDiv.classList.remove('alert-info');
                    pagesAmountProgressDiv.classList.add('alert-danger');
                } else {
                    pagesAmountProgressDiv.classList.add('hide');
                    pagesAmountSuccessDiv.classList.remove('hide');
                    pagesAmountSuccessDiv.innerHTML = pagesAmountSuccessDiv.innerHTML.replace('%d', response.amount);
                    YoastCrawler.runIndex(site, language);
                }
            });
    },

    setButtonsDisableStatus: (disableStatus) => {
        let buttons = document.querySelectorAll('.js-crawler-button');
        buttons.forEach((button) => {
            button.disabled = disableStatus;
        });
    },

    runIndex: (site, language) => {
        let progressDiv = document.querySelector('#progress-' + site + '-' + language);
        let progressBar = progressDiv.querySelector('.js-crawler-progress-bar');

        YoastTypo3.postRequest(YoastConfig.urls.indexPages, {
            site: site,
            language: language,
            offset: typeof YoastCrawler._progressCache[site + '-' + language] !== "undefined" ? parseInt(YoastCrawler._progressCache[site + '-' + language]) : 0
        })
            .then((response) => response.json())
            .then(async (response) => {
                if (typeof response.status !== "undefined") {
                    YoastCrawler.setButtonsDisableStatus(false);
                    progressBar.classList.remove('progress-bar-info');
                    progressBar.classList.add('progress-bar-success');
                    progressBar.style.width = '100%';
                    progressBar.innerHTML = response.total + ' pages have been indexed.';
                } else {
                    YoastCrawler._progressCache[site + '-' + language] = parseInt(response.nextOffset);
                    await YoastCrawler.process(site, language, response, progressBar);
                    YoastCrawler.runIndex(site, language, response);
                }
            });
    },

    process: async (site, language, response, progressBar) => {
        let current = response.current;
        let total = response.total;
        let percentage = Math.round((current / total) * 100);

        YoastCrawler.updateProgressBar(progressBar, current, total, percentage);
        for (let page in response.pages) {
            if (response.pages.hasOwnProperty(page)) {
                let pageId = response.pages[page];
                let previewResponse = await YoastTypo3.postRequest(YoastConfig.urls.preview, {
                    pageId: pageId,
                    languageId: language,
                    additionalGetVars: ''
                })
                if (previewResponse.ok) {
                    let previewJson = await previewResponse.json();
                    await YoastCrawler.processRelevantWords(pageId, language, previewJson);
                    current++;
                    percentage = Math.round((current / total) * 100);
                    YoastCrawler.updateProgressBar(progressBar, current, total, percentage);
                }
            }
        }
    },

    processRelevantWords: (page, language, response) => {
        YoastTypo3.setWorker(false, response.locale);
        let paper = new Paper(response.body, {
            title: response.title,
            description: response.description,
            locale: response.locale
        });
        return YoastTypo3._yoastWorker.runResearch('getProminentWordsForInternalLinking', paper)
            .then(async (results) => {
                await YoastCrawler.saveRelevantWords(page, language, results);
            });
    },

    saveRelevantWords: (page, language, result) => {
        let words = result.result.prominentWords.slice(0, 25);

        let compressedWords = {};
        words.forEach((word) => {
            compressedWords[word.getStem()] = word.getOccurrences();
        });

        YoastTypo3.postRequest(YoastConfig.urls.prominentWords, {
            words: compressedWords,
            uid: page,
            languageId: language,
            table: 'pages'
        });
    },

    updateProgressBar: (progressBar, current, total, percentage) => {
        progressBar.innerText = current + '/' + total;
        progressBar.style.width = percentage + '%';
    }
}

YoastTypo3.init();
