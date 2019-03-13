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
        synonyms: '',
        description: data.description,
        titleWidth: measureTextWidth(data.title)
    });

    const relatedKeyphrases = {
        een: {
            keyword: 'bla',
            synonyms: 'beproeving, experiment, onderzoek, poging, probatie, probeersel, proefje, proefneming, test, test-case, toetsing, trial. keuring (zn) : beoordeling, controle, essaai, nazicht, onderzoek, proefneming, schouwing, test, toets, toetsing'
        },
        twee: {
            keyword: '',
            synonyms: ''
        }
    }

    const promises = [
        store.dispatch(analyzeData(worker, paper, relatedKeyphrases)),
        store.dispatch(getRelevantWords(worker, paper)),
    ];

    return Promise.all(promises);
}