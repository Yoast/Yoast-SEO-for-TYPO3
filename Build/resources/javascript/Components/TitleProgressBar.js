import React, {useEffect, useState} from 'react';
import {connect} from 'react-redux';
import { ProgressBar } from '@yoast/components';
import getProgressColor from '../helpers/progressColor';
import measureTextWidth from '../helpers/measureTextWidth';
import { assessments } from 'yoastseo';

const getTitleProgress = (title) => {
    const titleWidth = measureTextWidth(title);
    const pageTitleWidthAssessment = new assessments.seo.PageTitleWidthAssessment();
    const score = pageTitleWidthAssessment.calculateScore(titleWidth);
    const maximumLength = pageTitleWidthAssessment.getMaximumLength();

    return {
        max: maximumLength,
        actual: titleWidth,
        score: score,
    };
}

const TitleProgressBar = ({title = ''}) => {
    const [titleProgress, setTitleProgress] = useState({
        progress: null,
        title: ''
    });

    useEffect(() => {
        setTitleProgress(prevState => {
            return {
                ...prevState, ...{
                    progress: getTitleProgress(title),
                    title: title
                }
            }
        });
    }, [title]);

    if (titleProgress.progress !== null) {
        return <ProgressBar min={0} max={titleProgress.progress.max} value={titleProgress.progress.actual}
                            progressColor={getProgressColor(titleProgress.progress.score)} />
    }
    return <></>
}

const mapStateToProps = (state) => {
    return {
        ...state.content
    }
}

export default connect(mapStateToProps)(TitleProgressBar);
