import React from 'react';
import createAnalysisWorker from './analysis/createAnalysisWorker';
import {Paper} from "yoastseo";
import store from "./redux/store";

var linkingSuggestions = {
    _url: '',
    _worker: null,
    _updateInterval: null,

    init: function (url) {
        this._url = url;
        this._worker = createAnalysisWorker(false, store.getState().content.locale);

        this._updateInterval = setInterval(() => {
            this.checkAnalysis();
        }, 1000);
    },

    checkAnalysis: function () {
        if (typeof CKEDITOR === "undefined") {
            return;
        }

        let content = this.getCKEditorContent();
        if (content === '') {
            return;
        }

        this.getLinkingSuggestions(content);

        // Analysis is done, Updating every second is not necessary anymore
        clearInterval(this._updateInterval);
        this._updateInterval = setInterval(() => {
            this.checkAnalysis();
        }, 10000);
    },

    getLinkingSuggestions: function (content) {
        const paper = new Paper( content, {});
        this._worker.runResearch('prominentWordsForInternalLinking', paper)
            .then((results) => {
                let words = results.result.prominentWords.slice( 0, 5 );

                fetch(this._url, {
                    method: 'post',
                    headers : new Headers(),
                    body: JSON.stringify({words: words, excludedPage: YoastConfig.linkingSuggestions.excludedPage, language: YoastConfig.data.languageId, content: content})
                })
                    .then(response => {
                        return response.json();
                    })
                    .then(results => {
                        this.updateLinkingSuggestions(results.links);
                    });
            });
    },

    updateLinkingSuggestions: function(links) {
        let content = '';

        if (links && links.length > 0) {
            content += '<table class="table" style="width: auto;">';
            content += '<thead><tr>' +
                '<th>Label</th><th>Record</th><th>Linked</th>' +
                '</tr></thead>' +
                '<tbody>';
            for (let link in links) {
                if (links.hasOwnProperty(link)) {
                    content += '<tr><td>';
                    content += links[link].label;
                    if (links[link].cornerstone === 1) {
                        content += ' *';
                    }
                    content += '</td><td>Page [uid=' + links[link].id + ']</td>';
                    if (links[link].active) {
                        content += '<td class="text-center"><btn class="btn btn-success btn-sm">&nbsp;&check;&nbsp;</btn></td>';
                    } else {
                        content += '<td class="text-center"><btn class="btn btn-danger btn-sm">&nbsp;&cross;&nbsp;</btn></td>';
                    }

                    content += '</tr>';
                }
            }
            content += '</tbody>' +
                '</table>';
        }

        if (content === '') {
            content = '&gt; No results found, make sure you have more than 100 words to analyze.';
        }

        document.querySelector(`[data-yoast-linking-suggestion]`).innerHTML = content;
    },

    getCKEditorContent: function () {
        let content = '';
        for (let instance in CKEDITOR.instances) {
            if (CKEDITOR.instances.hasOwnProperty(instance)) {
                content += CKEDITOR.instances[instance].getData();
            }
        }
        return content;
    }
};

if (typeof YoastConfig.urls.linkingSuggestions !== "undefined") {
    linkingSuggestions.init(YoastConfig.urls.linkingSuggestions);
}
