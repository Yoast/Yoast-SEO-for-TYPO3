import {UPDATE_TWITTER_PREVIEW} from '../actions/twitterPreview';

const initialState = '';

export default (state = initialState, action) => {
    switch(action.type) {
        case UPDATE_TWITTER_PREVIEW:
            return {...state, ...action.payload};
        default:
            return state;
    }
};