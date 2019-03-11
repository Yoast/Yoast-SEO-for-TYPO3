import store from '../store';
import {analyzeData} from '../actions/analysis';
import {getRelevantWords} from '../actions/relevantWords';

export const GET_CONTENT_REQUEST = 'GET_CONTENT_REQUEST';
export const GET_CONTENT_SUCCESS = 'GET_CONTENT_SUCCESS';
export const GET_CONTENT_ERROR = 'GET_CONTENT_ERROR';

export function getContent(keyword, synonyms, useCornerstone) {
    return dispatch => {
        dispatch({type: GET_CONTENT_REQUEST});

        fetch(tx_yoast_seo.settings.preview)
            .then(response => {
                return response.json();
            })
            .then(data => {
                if (!data.description) {
                    const bodyText = document.createElement('div');
                    bodyText.innerHTML = data.body;
                    data.description = bodyText.innerText;
                }

                const workerUrl = '/typo3conf/ext/yoast_seo/Resources/Public/JavaScript/dist/worker.js';
                dispatch({type: GET_CONTENT_SUCCESS, payload: data});
                store.dispatch(analyzeData(data, keyword, synonyms, workerUrl, useCornerstone));
                store.dispatch(getRelevantWords(data, keyword, synonyms, workerUrl, useCornerstone));
            })
            .catch(error => {
                dispatch({type: GET_CONTENT_ERROR, payload: error, error: true});
            });
    };
}
