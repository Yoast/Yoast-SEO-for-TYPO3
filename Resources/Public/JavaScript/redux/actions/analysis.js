import {AnalysisWorkerWrapper, createWorker, Paper} from "yoastseo";

export const ANALYZE_DATA_REQUEST = 'ANALYZE_DATA_REQUEST';
export const ANALYZE_DATA_SUCCESS = 'ANALYZE_DATA_SUCCESS';

export function analyzeData(body, keyword) {
    return dispatch => {
        dispatch({type: ANALYZE_DATA_REQUEST});

        const url = "https://richardhaeser.ddev.local/typo3conf/ext/yoast_seo/Resources/Public/JavaScript/dist/worker.js"
        const worker = new AnalysisWorkerWrapper( createWorker( url ) );

        worker.initialize( {
            locale: "en_US",
            contentAnalysisActive: true,
            keywordAnalysisActive: true,
            logLevel: "ERROR",
        } ).then( () => {
            // The worker has been configured, we can now analyze a Paper.
            const paper = new Paper( body, {
                keyword: keyword,
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
