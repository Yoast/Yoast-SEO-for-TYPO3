import React from 'react';
import {ContentAnalysis as YoastContentAnalysis} from '@yoast/analysis-report';
import LoadingSpinner from "./LoadingSpinner";

const Analysis = ({analysis}) => {
    if (!analysis) {
        return <LoadingSpinner/>
    }

    const {
        errorsResults,
        improvementsResults,
        goodResults,
        considerationsResults,
        problemsResults,
    } = analysis;
    const marksButtonStatus = 'disabled';

    return <YoastContentAnalysis
        problemsResults={problemsResults}
        improvementsResults={improvementsResults}
        goodResults={goodResults}
        considerationsResults={considerationsResults}
        errorsResults={errorsResults}
        marksButtonStatus={marksButtonStatus}
    />
}

export default Analysis;
