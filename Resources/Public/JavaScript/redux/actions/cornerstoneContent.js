import store from "../store";
import {getContent} from "./content";

export const SET_CORNERSTONE_CONTENT = 'SET_CORNERSTONE_CONTENT';

export function setCornerstoneContent(isCornerstoneContent) {
    let state = store.getState();

    store.dispatch(getContent(state.focusKeyword, '', isCornerstoneContent));
    return {type: SET_CORNERSTONE_CONTENT, isCornerstoneContent};
}
