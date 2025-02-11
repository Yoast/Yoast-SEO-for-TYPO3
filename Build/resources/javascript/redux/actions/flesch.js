export const GET_FLESCH_REQUEST = 'GET_FLESCH_REQUEST';
export const GET_FLESCH_SUCCESS = 'GET_FLESCH_SUCCESS';

export function getFleschReadingScore(worker, paper) {
    return dispatch => {
        dispatch({type: GET_FLESCH_REQUEST});

        return worker
            .runResearch('getFleschReadingScore', paper)
            .then((results) => {
                dispatch({type: GET_FLESCH_SUCCESS, payload: results});
            }).catch((error) => {
                console.error('An error occured while analyzing the text:');
                console.error(error);
            });
    };
}
