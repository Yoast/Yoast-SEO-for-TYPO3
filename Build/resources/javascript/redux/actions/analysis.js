import {interpreters} from 'yoastseo';

export const ANALYZE_DATA_REQUEST = 'ANALYZE_DATA_REQUEST';
export const ANALYZE_DATA_SUCCESS = 'ANALYZE_DATA_SUCCESS';

const saveScores = (results) => {
    const {scoreToRating} = interpreters;

    if (typeof YoastConfig.data !== "undefined" && typeof YoastConfig.urls.saveScores !== "undefined") {
        fetch(YoastConfig.urls.saveScores, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                table: YoastConfig.data.table,
                uid: YoastConfig.data.uid,
                languageId: YoastConfig.data.languageId,
                readabilityScore: scoreToRating(results.result.readability.score / 10),
                seoScore: scoreToRating(results.result.seo[''].score / 10)
            })
        });
    }
}

const analyzeError = (error) => {
    console.error('An error occured while analyzing the text:');
    console.error(error);
}

export function analyzeData(worker, paper, relatedKeyphrases = undefined) {
    return dispatch => {
        dispatch({type: ANALYZE_DATA_REQUEST});

        if (relatedKeyphrases !== undefined && typeof relatedKeyphrases === "object" && Object.keys(relatedKeyphrases).length > 0) {
            return worker.analyze(paper)
                .then((results) => {
                    saveScores(results);
                    worker.analyzeRelatedKeywords(paper, relatedKeyphrases)
                        .then((keywordResults) => {
                            results.result.seo = {...results.result.seo, ...keywordResults.result.seo};
                            dispatch({type: ANALYZE_DATA_SUCCESS, payload: results});
                        })
                        .catch((error) => analyzeError(error));
                }).catch((error) => analyzeError(error));
        }

        let request;
        if (relatedKeyphrases === undefined) {
            request = worker.analyze(paper);
        } else {
            request = worker.analyzeRelatedKeywords(paper, relatedKeyphrases);
        }
        return request
            .then((results) => {
                saveScores(results);
                dispatch({type: ANALYZE_DATA_SUCCESS, payload: results});
            }).catch((error) => analyzeError(error));
    };
}
