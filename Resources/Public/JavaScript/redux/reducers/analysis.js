import {ANALYZE_DATA_REQUEST, ANALYZE_DATA_SUCCESS, analyzeData} from '../actions/analysis';

const initialState = {
    isAnalyzing: false,
    result: {
        readability: {
            results: []
        }
    }
};

function analysisReducer (state = initialState, action) {
    switch(action.type) {
        case ANALYZE_DATA_REQUEST:
            return {...state, isAnalyzing: true};
        case ANALYZE_DATA_SUCCESS:
            return {...state, isAnalyzing: false, ...action.payload};
        default:
            return state;
    }
};

export default analysisReducer;
