import {Paper} from "yoastseo";

export const GET_LINKINGSUGGESTIONS_SUCCESS = 'GET_LINKINGSUGGESTIONS_SUCCESS';

export function getLinkingSuggestions(worker, content, url) {
    return dispatch => {
        const paper = new Paper(content, {
            locale: YoastConfig.linkingSuggestions.locale
        });

        return worker.runResearch('getProminentWordsForInternalLinking', paper)
            .then((results) => {
                let words = results.result.prominentWords.slice(0, 5);

                fetch(url, {
                    method: 'post',
                    headers: new Headers(),
                    body: JSON.stringify({
                        words: words,
                        excludedPage: YoastConfig.linkingSuggestions.excludedPage,
                        languageId: YoastConfig.data.languageId,
                        content: content
                    })
                })
                    .then(response => {
                        return response.json();
                    })
                    .then(results => {
                        dispatch({type: GET_LINKINGSUGGESTIONS_SUCCESS, payload: results});
                    })
                    .catch((error) => {
                        console.error('An error occured while analyzing the linking suggestions:');
                        console.error(error);
                    });
            });
    };
}
