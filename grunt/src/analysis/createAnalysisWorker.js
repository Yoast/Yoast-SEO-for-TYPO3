import {AnalysisWorkerWrapper, createWorker} from 'yoastseo';

export default function createAnalysisWorker(useCornerstone, locale) {
    const worker = new AnalysisWorkerWrapper( createWorker( YoastConfig.urls.workerUrl ) );

    worker.initialize( {
        locale: locale,
        contentAnalysisActive: true,
        keywordAnalysisActive: true,
        useKeywordDistribution: true,
        useCornerstone: useCornerstone,
        logLevel: "ERROR",
        translations: YoastConfig.translations
    });

    return worker;
};
