export const SET_FOCUS_KEYWORD_SYNONYMS = 'SET_FOCUS_KEYWORD_SYNONYMS';

export function setFocusKeywordSynonyms(synonyms) {
    return {type: SET_FOCUS_KEYWORD_SYNONYMS, synonyms};
}
