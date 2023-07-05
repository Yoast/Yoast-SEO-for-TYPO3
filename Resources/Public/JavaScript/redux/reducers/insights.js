import {GET_INSIGHTS_REQUEST, GET_INSIGHTS_SUCCESS} from '../actions/insights';

const initialState = {
    isCheckingInsights: false,
    words: []
};

function insightsReducer (state = initialState, action) {
    switch(action.type) {
        case GET_INSIGHTS_REQUEST:
            return {...state, isCheckingInsights: true};
        case GET_INSIGHTS_SUCCESS:
            return {...state, isCheckingInsights: false, insights: action.payload};
        default:
            return state;
    }
}

export default insightsReducer;
