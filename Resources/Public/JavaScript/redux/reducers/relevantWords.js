import {GET_RELEVANTWORDS_REQUEST, GET_RELEVANTWORDS_SUCCESS} from '../actions/relevantWords';

const initialState = {
    isCheckingRelevantWords: false,
    words: []
};

function relevantWordsReducer (state = initialState, action) {
    switch(action.type) {
        case GET_RELEVANTWORDS_REQUEST:
            return {...state, isCheckingRelevantWords: true};
        case GET_RELEVANTWORDS_SUCCESS:
            return {...state, isCheckingRelevantWords: false, relevantWords: action.payload};
        default:
            return state;
    }
};

export default relevantWordsReducer;
