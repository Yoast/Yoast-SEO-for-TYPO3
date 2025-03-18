import {store} from "@yoast/yoast-seo-for-typo3/store.js";
import Paper from "@yoast/yoast-seo-for-typo3/paper.js";
import measureTextWidth from "@yoast/yoast-seo-for-typo3/helpers/text-width.js";
import worker from "@yoast/yoast-seo-for-typo3/web-worker.js";

class Analysis {
  private static instance: Analysis;

  private constructor() {
  }

  public static getInstance(): Analysis {
    if (!Analysis.instance) {
      Analysis.instance = new Analysis();
    }
    return Analysis.instance;
  }

  public refresh(): void {
    const state = store.getState();
    const paper = new Paper(state.body, {
      keyword: state.focusKeyword,
      title: state.title,
      synonyms: state.synonyms,
      description: state.description,
      locale: state.locale,
      titleWidth: measureTextWidth(state.titleWidth),
    })
    worker.set(state.cornerstone, state.locale);
    worker.sendRequest('analyze', {paper: paper.serialize()})
      .then((response) => {
        console.log(response.result);
        store.setState(response.result)
      })
      .catch((error) => {
        console.error('yoast-seo analysis failed', error);
      });
  }
}

export const analysis = Analysis.getInstance();
