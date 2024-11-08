import {SET_FOCUS_KEYWORD_SYNONYMS} from '../actions/focusKeywordSynonyms';

const initialState = '';

export default (state = initialState, action) => {
    switch(action.type) {
        case SET_FOCUS_KEYWORD_SYNONYMS:
            return action.synonyms;
        default:
            return state;
    }
}
