import {AnalysisWorkerWrapper, createWorker} from 'yoastseo';

const workerUrl = '/typo3conf/ext/yoast_seo/Resources/Public/JavaScript/dist/worker.js';

export default function createAnalysisWorker(useCornerstone, locale) {
    const worker = new AnalysisWorkerWrapper( createWorker( workerUrl ) );

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
