import {SET_CORNERSTONE_CONTENT} from '../actions/cornerstoneContent';

const initialState = false;

export default (state = initialState, action) => {
    switch(action.type) {
        case SET_CORNERSTONE_CONTENT:
            return action.useCornerstone;
        default:
            return state;
    }
}
