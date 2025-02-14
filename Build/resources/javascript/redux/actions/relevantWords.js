export const GET_RELEVANTWORDS_REQUEST = 'GET_RELEVANTWORDS_REQUEST';
export const GET_RELEVANTWORDS_SUCCESS = 'GET_RELEVANTWORDS_SUCCESS';
export const SAVE_RELEVANTWORDS_SUCCESS = 'SAVE_RELEVANTWORDS_SUCCESS';

export function getRelevantWords(worker, paper) {
    return dispatch => {
        dispatch({type: GET_RELEVANTWORDS_REQUEST});

        return worker
            .runResearch('getProminentWordsForInternalLinking', paper)
            .then((results) => {
                dispatch({type: GET_RELEVANTWORDS_SUCCESS, payload: results});
            }).catch((error) => {
                console.error('An error occured while analyzing the text:');
                console.error(error);
            });
    };
}

export function saveRelevantWords(object, uid, pid, languageId, table, url)
{
    return dispatch => {
        let words = object.relevantWords.result.prominentWords.slice( 0, 25 );

        let compressedWords = {};
        words.forEach( function( word ) {
            compressedWords[ word.getStem() ] = word.getOccurrences();
        } );

        if (url) {
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({words: compressedWords, uid: uid, pid: pid, languageId: languageId, table: table})
            });
        }

        dispatch({type: SAVE_RELEVANTWORDS_SUCCESS});
    };
}
