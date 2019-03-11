import {AnalysisWorkerWrapper, createWorker, Paper} from "yoastseo";
import measureTextWidth from "../../helpers/measureTextWidth";

export const GET_RELEVANTWORDS_REQUEST = 'GET_RELEVANTWORDS_REQUEST';
export const GET_RELEVANTWORDS_SUCCESS = 'GET_RELEVANTWORDS_SUCCESS';

export function getRelevantWords(data, keyword, synonyms, url, useCornerstone) {
    return dispatch => {
        dispatch({type: GET_RELEVANTWORDS_REQUEST});

        const worker = new AnalysisWorkerWrapper( createWorker( url ) );

        worker.initialize( {
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
