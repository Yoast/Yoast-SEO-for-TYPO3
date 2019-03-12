import store from "../store";
import {analyzeData} from "./analysis";

export const SET_CORNERSTONE_CONTENT = 'SET_CORNERSTONE_CONTENT';

export function setCornerstoneContent(useCornerstone, workerUrl) {
    let state = store.getState();

    store.dispatch(analyzeData(state.content, state.focusKeyword, '', workerUrl, useCornerstone));

    return {type: SET_CORNERSTONE_CONTENT, useCornerstone};
}
