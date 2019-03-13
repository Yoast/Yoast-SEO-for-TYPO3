import {AnalysisWorkerWrapper, createWorker} from 'yoastseo';

export const ANALYZE_DATA_REQUEST = 'ANALYZE_DATA_REQUEST';
export const ANALYZE_DATA_SUCCESS = 'ANALYZE_DATA_SUCCESS';

export function analyzeData(worker, paper, relatedKeyphrases = undefined) {
    return dispatch => {
        dispatch({type: ANALYZE_DATA_REQUEST});

        let request;
        if (relatedKeyphrases === undefined) {
            request = worker.analyze(paper);
        } else {
            request = worker.analyzeRelatedKeywords(paper, relatedKeyphrases);
        }

        return request
            .then((results) => {
                dispatch({type: ANALYZE_DATA_SUCCESS, payload: results});
            }).catch((error) => {
                console.error('An error occured while analyzing the text:');
                console.error(error);
            });
    };
}
