import {GET_CONTENT_REQUEST, GET_CONTENT_SUCCESS, GET_CONTENT_ERROR, UPDATE_CONTENT} from '../actions/content';

const initialState = {
    isFetching: false
};

let updateQueue = [];

function contentReducer (state = initialState, action) {
    switch(action.type) {
        case GET_CONTENT_REQUEST:
            return {...state, isFetching: true};
        case GET_CONTENT_SUCCESS:
            YoastConfig.pageTitlePrepend = action.payload.pageTitlePrepend;
            YoastConfig.pageTitleAppend = action.payload.pageTitleAppend;

            let newState = {...state, isFetching: false, ...action.payload};

            updateQueue.forEach((action) => {
                newState = contentReducer(newState, action)
            })
            updateQueue = []

            return newState
        case GET_CONTENT_ERROR:
            return {...state, isFetching: false, error: action.error}
        case UPDATE_CONTENT:
            // Queue content updates until content has been fetched. Otherwise partial updates will break component props assumptions.
            if (state.isFetching) {
                updateQueue.push(action);
                return state;
            }
            return {...state, isFetching: false, ...action.payload}
        default:
            return state;
    }
};

export default contentReducer;
