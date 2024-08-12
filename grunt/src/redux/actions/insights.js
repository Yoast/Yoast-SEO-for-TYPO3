export const GET_INSIGHTS_REQUEST = 'GET_INSIGHTS_REQUEST';
export const GET_INSIGHTS_SUCCESS = 'GET_INSIGHTS_SUCCESS';

export function getInsights(worker, paper) {
    return dispatch => {
        dispatch({type: GET_INSIGHTS_REQUEST});

        return worker
            .runResearch('prominentWordsForInsights', paper)
            .then((results) => {
                dispatch({type: GET_INSIGHTS_SUCCESS, payload: results});
            }).catch((error) => {
                console.error('An error occured while analyzing the text:');
                console.error(error);
            });
    };
}
