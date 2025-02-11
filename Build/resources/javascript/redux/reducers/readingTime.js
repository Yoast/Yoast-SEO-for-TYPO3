import {GET_READINGTIME_REQUEST, GET_READINGTIME_SUCCESS} from '../actions/readingTime';

const initialState = {
    isCheckingReadingTime: false
};

function readingTimeReducer (state = initialState, action) {
    switch(action.type) {
        case GET_READINGTIME_REQUEST:
            return {...state, isCheckingReadingTime: true};
        case GET_READINGTIME_SUCCESS:
            return {...state, isCheckingReadingTime: false, readingTime: action.payload};
        default:
            return state;
    }
}

export default readingTimeReducer;
