import {AnalysisWorkerWrapper, createWorker, Paper} from "yoastseo";
import measureTextWidth from "../../helpers/measureTextWidth";

export const ANALYZE_DATA_REQUEST = 'ANALYZE_DATA_REQUEST';
export const ANALYZE_DATA_SUCCESS = 'ANALYZE_DATA_SUCCESS';

export function analyzeData(data, keyword, synonyms, url) {
    return dispatch => {
        dispatch({type: ANALYZE_DATA_REQUEST});

        const worker = new AnalysisWorkerWrapper( createWorker( url ) );

        worker.initialize( {
            locale: "en_US",
            contentAnalysisActive: true,
            keywordAnalysisActive: true,
            logLevel: "ERROR",
        } ).then( () => {
            const paper = new Paper( data.body, {
                keyword: keyword,
                title: data.title,
                synonyms: synonyms,
                description: data.description,
                titleWidth: measureTextWidth(data.title)
            } );

            return worker.analyze( paper );
        } ).then( ( results ) => {
            dispatch({type: ANALYZE_DATA_SUCCESS, payload: results});
        } ).catch( ( error ) => {
            console.error( 'An error occured while analyzing the text:' );
            console.error( error );
        } );
    };
}
