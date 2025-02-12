import {UPDATE_FACEBOOK_PREVIEW} from '../actions/facebookPreview';

const initialState = '';

export default (state = initialState, action) => {
    switch(action.type) {
        case UPDATE_FACEBOOK_PREVIEW:
            return {...state, ...action.payload};
        default:
            return state;
    }
};