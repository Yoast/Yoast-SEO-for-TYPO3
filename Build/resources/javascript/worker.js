import { AnalysisWebWorker } from "yoastseo";

self.onmessage = ( event ) => {
  // Set the language for the Researcher
  const language = event.data.language;
  const { "default": Researcher } = require( `yoastseo/build/languageProcessing/languages/${language}/Researcher` );
  const researcher = new Researcher();
  const worker = new AnalysisWebWorker( self, researcher );
  worker.register();
};
