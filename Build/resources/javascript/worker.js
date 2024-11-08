import { AnalysisWebWorker } from "yoastseo";
import EnglishResearcher from "yoastseo/build/languageProcessing/languages/en/Researcher";

const worker = new AnalysisWebWorker( self, new EnglishResearcher() );
worker.register();