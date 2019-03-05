// import { AnalysisWorkerWrapper, createWorker, Paper } from "yoastseo";
//
// const url = "https://frank-typo3.dev.maxserv.com/typo3conf/ext/yoast_seo/Resources/Public/JavaScript/dist/worker.js"
//
// const worker = new AnalysisWorkerWrapper( createWorker( url ) );
//
// worker.initialize( {
//     locale: "en_US",
//     contentAnalysisActive: true,
//     keywordAnalysisActive: true,
//     logLevel: "ERROR",
// } ).then( () => {
//     // The worker has been configured, we can now analyze a Paper.
//     const paper = new Paper( "Text to analyze", {
//         keyword: "analyze",
//     } );
//
//     return worker.analyze( paper );
// } ).then( ( results ) => {
//     console.log( 'Analysis results:' );
//     console.log( results );
// } ).catch( ( error ) => {
//     console.error( 'An error occured while analyzing the text:' );
//     console.error( error );
// } );

import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import SnippetPreview from './Components/SnippetPreview';
import store from './redux/store';
import {getPreview} from './redux/actions/preview';

let keyword = tx_yoast_seo.settings.focusKeyword;

store.dispatch(getPreview());

document.querySelectorAll('[data-yoast-snippetpreview]').forEach(container => {
    ReactDOM.render(<Provider store={store}><SnippetPreview keyword={keyword} /></Provider>, container);
});