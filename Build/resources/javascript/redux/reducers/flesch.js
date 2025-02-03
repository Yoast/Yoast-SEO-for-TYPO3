import {GET_FLESCH_REQUEST, GET_FLESCH_SUCCESS} from '../actions/flesch';

const initialState = {
    isCheckingFlesch: false,
    words: []
};

function fleschReducer (state = initialState, action) {
    switch(action.type) {
        case GET_FLESCH_REQUEST:
            return {...state, isCheckingFlesch: true};
        case GET_FLESCH_SUCCESS:
            return {...state, isCheckingFlesch: false, flesch: action.payload};
        default:
            return state;
    }
}

export default fleschReducer;
