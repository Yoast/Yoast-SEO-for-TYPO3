import {GET_WORDCOUNT_REQUEST, GET_WORDCOUNT_SUCCESS} from '../actions/wordCount';

const initialState = {
    isCheckingWordCount: false
};

function wordCountReducer (state = initialState, action) {
    switch(action.type) {
        case GET_WORDCOUNT_REQUEST:
            return {...state, isCheckingWordCount: true};
        case GET_WORDCOUNT_SUCCESS:
            return {...state, isCheckingWordCount: false, wordCount: action.payload};
        default:
            return state;
    }
}

export default wordCountReducer;
