import React from 'react';
import createAnalysisWorker from './analysis/createAnalysisWorker';
import {Paper} from "yoastseo";
import store from "./redux/store";

function updateLinkingSuggestions(url) {
    let timeout = 1000; // check every second when no analysis is done

    if (typeof CKEDITOR != "undefined") {
        let content = '';

        for (let instance in CKEDITOR.instances) {
            content += CKEDITOR.instances[instance].getData();
        }

        if (content) {
            let worker = createAnalysisWorker(false, store.getState().content.locale);

            const paper = new Paper( content, {});
            let request;
            request = worker.runResearch('relevantWords', paper)
                .then((results) => {
                    let words = results.result.slice( 0, 5 );

                    fetch(url, {
                        method: 'post',
                        headers : new Headers(),
                        body: JSON.stringify({words: words, excludedPage: YoastConfig.linkingSuggestions.excludedPage, language: YoastConfig.data.languageId})
                    })
                        .then(response => {
                            return response.json();
                        })
                        .then(results => {
                            let content = '';

                            for (let word in results.links) {
                                content += '<p><strong>' + word + '</strong></p>';
                                content += '<ol>';
                                for (let link in results.links[word]) {
                                    let cornerstone = '';
                                    if (results.links[word][link]['cornerstone'] == 1) {
                                        cornerstone = ' *';
                                    }

                                    content += '<li>' + results.links[word][link]['label'] + cornerstone + ' [ID: ' + results.links[word][link]['id'] + ']</li>';
                                }
                                content += '</ol>';
                            }

                            if (content == '') {
                                content = '> No results found';
                            }

                            document.querySelector(`[data-yoast-linking-suggestion]`).innerHTML = content;
                        });
                });

            timeout = 10000; // we have done an analysis so updating every second is not nessecary anymore.
        }
    }

    setTimeout(function() {
        updateLinkingSuggestions(url);
    }, timeout);
}

if (typeof YoastConfig.urls.linkingSuggestions != "undefined") {
    updateLinkingSuggestions(YoastConfig.urls.linkingSuggestions);
}
