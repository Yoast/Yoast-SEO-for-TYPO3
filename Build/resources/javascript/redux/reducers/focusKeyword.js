import {SET_FOCUS_KEYWORD} from '../actions/focusKeyword';

const initialState = '';

export default (state = initialState, action) => {
    switch(action.type) {
        case SET_FOCUS_KEYWORD:
            return action.keyword;
        default:
            return state;
    }
}
