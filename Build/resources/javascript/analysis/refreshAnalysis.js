import {Paper} from 'yoastseo';
import measureTextWidth from '../helpers/measureTextWidth';
import {analyzeData} from '../redux/actions/analysis';
import {getRelevantWords} from '../redux/actions/relevantWords';
import {getInsights} from "../redux/actions/insights";
import {getFleschReadingScore} from "../redux/actions/flesch";

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

    return Promise.all([
        store.dispatch(analyzeData(worker, paper, YoastConfig.relatedKeyphrases)),
        store.dispatch(getRelevantWords(worker, paper)),
        store.dispatch(getInsights(worker, paper)),
        store.dispatch(getFleschReadingScore(worker, paper)),
    ]);
}
