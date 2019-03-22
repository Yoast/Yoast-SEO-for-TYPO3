import {AnalysisWorkerWrapper, createWorker, helpers} from 'yoastseo';

export const ANALYZE_DATA_REQUEST = 'ANALYZE_DATA_REQUEST';
export const ANALYZE_DATA_SUCCESS = 'ANALYZE_DATA_SUCCESS';

export function analyzeData(worker, paper, relatedKeyphrases = undefined) {
    return dispatch => {
        const { scoreToRating } = helpers;

        dispatch({type: ANALYZE_DATA_REQUEST});

        let request;
        if (relatedKeyphrases === undefined) {
            request = worker.analyze(paper);
        } else {
            request = worker.analyzeRelatedKeywords(paper, relatedKeyphrases);
        }

        return request
            .then((results) => {
                if (typeof YoastConfig.data !== "undefined" && typeof YoastConfig.urls.saveScores !== "undefined") {
                    fetch(YoastConfig.urls.saveScores, {
                        method: 'post',
                        headers : new Headers(),
                        body: JSON.stringify({
                            table: YoastConfig.data.table,
                            uid: YoastConfig.data.uid,
                            languageId: YoastConfig.data.languageId,
                            readabilityScore: scoreToRating(results.result.readability.score / 10),
                            seoScore: scoreToRating(results.result.seo[''].score / 10)
                        })
                    });
                }

                dispatch({type: ANALYZE_DATA_SUCCESS, payload: results});
            }).catch((error) => {
                console.error('An error occured while analyzing the text:');
                console.error(error);
            });
    };
}
