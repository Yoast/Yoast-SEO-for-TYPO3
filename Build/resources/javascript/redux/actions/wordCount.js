export const GET_WORDCOUNT_REQUEST = 'GET_WORDCOUNT_REQUEST';
export const GET_WORDCOUNT_SUCCESS = 'GET_WORDCOUNT_SUCCESS';

export function getWordCount(worker, paper) {
    return dispatch => {
        dispatch({type: GET_WORDCOUNT_REQUEST});

        return worker
            .runResearch('wordCountInText', paper)
            .then((results) => {
                dispatch({type: GET_WORDCOUNT_SUCCESS, payload: results});
            }).catch((error) => {
                console.error('An error occured while analyzing the text:');
                console.error(error);
            });
    };
}
