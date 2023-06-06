import {GET_LINKINGSUGGESTIONS_SUCCESS} from '../actions/linkingSuggestions';

const initialState = {
    isCheckingLinkingSuggestions: true,
    suggestions: []
};

function linkingSuggestionsReducer (state = initialState, action) {
    switch(action.type) {
        case GET_LINKINGSUGGESTIONS_SUCCESS:
            return {...state, isCheckingLinkingSuggestions: false, suggestions: action.payload};
        default:
            return state;
    }
}

export default linkingSuggestionsReducer;
