import {AnalysisWorkerWrapper, createWorker, Paper} from 'yoastseo';
import measureTextWidth from '../../helpers/measureTextWidth';

export const GET_RELEVANTWORDS_REQUEST = 'GET_RELEVANTWORDS_REQUEST';
export const GET_RELEVANTWORDS_SUCCESS = 'GET_RELEVANTWORDS_SUCCESS';
export const SAVE_RELEVANTWORDS_SUCCESS = 'SAVE_RELEVANTWORDS_SUCCESS';

export function getRelevantWords(worker, paper) {
    return dispatch => {
        dispatch({type: GET_RELEVANTWORDS_REQUEST});

        return worker
            .runResearch('relevantWords', paper)
            .then((results) => {
                dispatch({type: GET_RELEVANTWORDS_SUCCESS, payload: results});
            }).catch((error) => {
                console.error('An error occured while analyzing the text:');
                console.error(error);
            });
    };
}

export function saveRelevantWords(object, uid, languageId, table, url)
{
    return dispatch => {
        let words = object.relevantWords.result.slice( 0, 25 );

        fetch(url, {
            method: 'post',
            headers : new Headers(),
            body: JSON.stringify({words: words, uid: uid, languageId: languageId, table: table})
        });

        dispatch({type: SAVE_RELEVANTWORDS_SUCCESS});
    };
}
