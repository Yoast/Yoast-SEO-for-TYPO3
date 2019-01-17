import { AnalysisWorkerWrapper, createWorker, Paper } from "yoastseo";

const url = "https://frank-typo3.dev.maxserv.com/typo3conf/ext/yoast_seo/Resources/Public/JavaScript/dist/worker.js"

const worker = new AnalysisWorkerWrapper( createWorker( url ) );

worker.initialize( {
    locale: "en_US",
    contentAnalysisActive: true,
    keywordAnalysisActive: true,
    logLevel: "ERROR",
} ).then( () => {
    // The worker has been configured, we can now analyze a Paper.
    const paper = new Paper( "Text to analyze", {
        keyword: "analyze",
    } );

    return worker.analyze( paper );
} ).then( ( results ) => {
    console.log( 'Analysis results:' );
    console.log( results );
} ).catch( ( error ) => {
    console.error( 'An error occured while analyzing the text:' );
    console.error( error );
} );