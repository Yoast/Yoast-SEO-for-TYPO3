export const GET_READINGTIME_REQUEST = 'GET_READINGTIME_REQUEST';
export const GET_READINGTIME_SUCCESS = 'GET_READINGTIME_SUCCESS';

export function getReadingTime(worker, paper) {
    return dispatch => {
        dispatch({type: GET_READINGTIME_REQUEST});

        return worker
            .runResearch('readingTime', paper)
            .then((results) => {
                dispatch({type: GET_READINGTIME_SUCCESS, payload: results});
            }).catch((error) => {
                console.error('An error occured while analyzing the text:');
                console.error(error);
            });
    };
}
