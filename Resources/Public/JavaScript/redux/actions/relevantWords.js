import {AnalysisWorkerWrapper, createWorker, Paper} from "yoastseo";
import measureTextWidth from "../../helpers/measureTextWidth";

export const GET_RELEVANTWORDS_REQUEST = 'GET_RELEVANTWORDS_REQUEST';
export const GET_RELEVANTWORDS_SUCCESS = 'GET_RELEVANTWORDS_SUCCESS';
export const SAVE_RELEVANTWORDS_SUCCESS = 'SAVE_RELEVANTWORDS_SUCCESS';

export function getRelevantWords(data, keyword, synonyms, url, useCornerstone) {
    return dispatch => {
        dispatch({type: GET_RELEVANTWORDS_REQUEST});

        const worker = new AnalysisWorkerWrapper( createWorker( url ) );

        return worker.initialize( {
            locale: "en_US",
            contentAnalysisActive: true,
            keywordAnalysisActive: true,
            useKeywordDistribution: true,
            useCornerstone: useCornerstone,
            logLevel: "ERROR",
        } ).then( () => {
            const paper = new Paper( data.body, {
                keyword: keyword,
                title: data.title,
                synonyms: synonyms,
                description: data.description,
                titleWidth: measureTextWidth(data.title)
            } );

            return worker.runResearch('relevantWords', paper);
        } ).then( ( results ) => {
            dispatch({type: GET_RELEVANTWORDS_SUCCESS, payload: results});
        } ).catch( ( error ) => {
            console.error( 'An error occured while analyzing the text:' );
            console.error( error );
        } );
    };
}

export function saveRelevantWords(object, pageId, languageId, url)
{
    return dispatch => {
        let words = object.relevantWords.result.slice( 0, 25 );

        fetch(url, {
            method: 'post',
            headers : new Headers(),
            body: JSON.stringify({words: words, pageId: pageId, languageId: languageId})
        });

        dispatch({type: SAVE_RELEVANTWORDS_SUCCESS});
    };
}
