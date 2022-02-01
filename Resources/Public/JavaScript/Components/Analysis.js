import React, {useEffect, useState} from 'react';
import {connect} from 'react-redux';
import LoadingIndicator from './LoadingIndicator';
import YoastContentAnalysis from 'yoast-components/composites/Plugin/ContentAnalysis/components/ContentAnalysis';
import {mapResults} from "../mapResults";
import getResult from '../getResult';

const Analysis = ({content, analysis, keyword, resultType, resultSubtype}) => {
    const [analysisResult, setAnalysisResult] = useState({
        currentAnalysis: false,
        mappedResults: {}
    });

    useEffect(() => {
        if (analysis.result[resultType] !== null) {
            let newMappedResults = mapResults(getResult(analysis, resultType, resultSubtype).results, keyword);
            if (analysisResult.currentAnalysis === false || analysis.result[resultType] !== analysisResult.currentAnalysis.result[resultType]) {
                setAnalysisResult({
                    currentAnalysis: analysis,
                    mappedResults: newMappedResults
                });
            }
        }
    }, [analysis, resultType, resultSubtype]);

    if (content.isFetching === false && analysis.isAnalyzing === false && getResult(analysis, resultType, resultSubtype)) {
        const {
            errorsResults,
            improvementsResults,
            goodResults,
            considerationsResults,
            problemsResults,
        } = analysisResult.mappedResults;
        const marksButtonStatus = 'disabled';

        return <YoastContentAnalysis
            problemsResults={problemsResults}
            improvementsResults={improvementsResults}
            goodResults={goodResults}
            considerationsResults={considerationsResults}
            errorsResults={errorsResults}
            marksButtonStatus={marksButtonStatus}
        />
    } else {
        return <LoadingIndicator />
    }
}

const mapStateToProps = (state) => {
    return {
        content: state.content,
        analysis: state.analysis,
        keyword: state.focusKeyword
    }
}

export default connect(mapStateToProps)(Analysis);
