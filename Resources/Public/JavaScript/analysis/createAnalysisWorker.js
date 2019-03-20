import {AnalysisWorkerWrapper, createWorker} from 'yoastseo';

const workerUrl = '/typo3conf/ext/yoast_seo/Resources/Public/JavaScript/dist/worker.js';

export default function createAnalysisWorker(useCornerstone) {
    const worker = new AnalysisWorkerWrapper( createWorker( workerUrl ) );

    worker.initialize( {
        locale: "en_US",
        contentAnalysisActive: true,
        keywordAnalysisActive: true,
        useKeywordDistribution: true,
        useCornerstone: useCornerstone,
        logLevel: "ERROR",
    });

    return worker;
};
