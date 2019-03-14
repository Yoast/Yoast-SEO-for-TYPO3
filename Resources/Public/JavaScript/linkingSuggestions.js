import React from 'react';
import createAnalysisWorker from './analysis/createAnalysisWorker';
import {Paper} from "yoastseo";

function updateLinkingSuggestions(url) {
    let timeout = 1000; // check every second when no analysis is done

    if (typeof CKEDITOR != "undefined") {
        let content = '';

        for (let instance in CKEDITOR.instances) {
            content += CKEDITOR.instances[instance].getData();
        }

        let worker = createAnalysisWorker(false);

        const paper = new Paper( content, {});
        let request;
        request = worker.runResearch('relevantWords', paper)
            .then((results) => {
                let words = results.result.slice( 0, 25 );

                fetch(url, {
                    method: 'post',
                    headers : new Headers(),
                    body: JSON.stringify({words: words})
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
                                content += '<li>[' + results.links[word][link]['table'] + ':' + results.links[word][link]['id'] + '] ' + results.links[word][link]['label'] + '</li>';
                            }
                            content += '</ol>';
                        }

                        document.querySelector(`[data-yoast-linking-suggestion]`).innerHTML = content;
                    });
            });

        timeout = 10000; // we have done an analysis so updating every second is not nessecary anymore.
    }

    setTimeout(function() {
        updateLinkingSuggestions(url);
    }, timeout);
}

if (typeof YoastConfig.urls.linkingSuggestions != "undefined") {
    updateLinkingSuggestions(YoastConfig.urls.linkingSuggestions);
}
