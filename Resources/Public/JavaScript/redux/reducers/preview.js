import {GET_PREVIEW_REQUEST, GET_PREVIEW_SUCCESS, GET_PREVIEW_ERROR} from '../actions/preview';

const initialState = {
    isFetching: false
};

function previewReducer (state = initialState, action) {
    switch(action.type) {
        case GET_PREVIEW_REQUEST:
            return {...state, isFetching: true};
        case GET_PREVIEW_SUCCESS:
            return {...state, isFetching: false, ...action.payload};
        case GET_PREVIEW_ERROR:
            return {...state, isFetching: false, error: action.error}
        default:
            return state;
    }
};

export default previewReducer;