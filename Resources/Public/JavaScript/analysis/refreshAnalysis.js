import store from '../redux/store';
import {Paper} from 'yoastseo';
import measureTextWidth from '../helpers/measureTextWidth';
import {analyzeData} from '../redux/actions/analysis';
import {getRelevantWords} from '../redux/actions/relevantWords';

export default function refreshAnalysis(worker, store) {
    const state = store.getState();
    const data = state.content;

    const paper = new Paper( data.body, {
        keyword: state.focusKeyword,
        title: data.title,
        synonyms: state.focusKeywordSynonyms,
        description: data.description,
        locale: data.locale,
        titleWidth: measureTextWidth(data.title)
    });

    let promises = [];
    if (typeof YoastConfig.useRelevantWords !== 'undefined' &&
        YoastConfig.useRelevantWords === true) {

        promises = [
            store.dispatch(analyzeData(worker, paper, YoastConfig.relatedKeyphrases)),
            store.dispatch(getRelevantWords(worker, paper)),
        ];
    } else {
        promises = [
            store.dispatch(analyzeData(worker, paper, YoastConfig.relatedKeyphrases)),
        ];
    }

    return Promise.all(promises);
}
