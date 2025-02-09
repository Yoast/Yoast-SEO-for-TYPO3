import {AnalysisWorkerWrapper, createWorker} from 'yoastseo';

const loadWebWorker = ( language ) => {
  const workerUnwrapped = createWorker( YoastConfig.urls.workerUrl );
  workerUnwrapped.postMessage( { language } );
  return new AnalysisWorkerWrapper( workerUnwrapped);
}

export default function createAnalysisWorker(useCornerstone, locale) {
    const worker = loadWebWorker(locale);

    worker.initialize( {
        locale: locale,
        contentAnalysisActive: true,
        keywordAnalysisActive: true,
        useKeywordDistribution: true,
        useCornerstone: useCornerstone,
        logLevel: "ERROR",
        translations: YoastConfig.translations,
        defaultQueryParams: null
    });

    return worker;
};
