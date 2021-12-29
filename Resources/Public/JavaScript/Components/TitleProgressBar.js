import React, {useEffect, useState} from 'react';
import { connect } from 'react-redux';
import ProgressBar from '@yoast/components/ProgressBar';
import getProgressColor from '../helpers/progressColor';
import measureTextWidth from '../helpers/measureTextWidth';
import PageTitleWidthAssessment from 'yoastseo/src/assessments/seo/PageTitleWidthAssessment';

const getTitleProgress = (title) => {
    const titleWidth = measureTextWidth( title );
    const pageTitleWidthAssessment = new PageTitleWidthAssessment();
    const score = pageTitleWidthAssessment.calculateScore( titleWidth );
    const maximumLength = pageTitleWidthAssessment.getMaximumLength();

    return {
        max: maximumLength,
        actual: titleWidth,
        score: score,
    };
}

const TitleProgressBar = ({title = ''}) => {
    const [progress, setProgress] = useState(null);
    const [currentTitle, setCurrentTitle] = useState('');

    useEffect(() => {
        if (title !== currentTitle) {
            setProgress(getTitleProgress(title));
            setCurrentTitle(title);
        }
    }, [title]);

    if (progress !== null) {
        return <ProgressBar max={progress.max} value={progress.actual} progressColor={getProgressColor(progress.score)} />
    }
    return <></>
}

const mapStateToProps = (state) => {
    return {
        ...state.content
    }
}

export default connect(mapStateToProps)(TitleProgressBar);
