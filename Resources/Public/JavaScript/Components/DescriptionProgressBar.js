import React, {useEffect, useState} from 'react';
import {connect} from 'react-redux';
import ProgressBar from '@yoast/components/ProgressBar';
import getProgressColor from '../helpers/progressColor';
import MetaDescriptionLengthAssessment from 'yoastseo/scoring/assessments/seo/MetaDescriptionLengthAssessment';

/**
 * Gets the description progress.
 *
 * @param {string} description The description.
 * @param {string} date        The meta description date
 *
 * @returns {Object} The description progress.
 */
const getDescriptionProgress = (description, date) => {
    let descriptionLength = description.length;
    /* If the meta description is preceded by a date, two spaces and a hyphen (" - ") are added as well. Therefore,
    three needs to be added to the total length. */
    if (date !== "" && descriptionLength > 0) {
        descriptionLength += date.length + 3;
    }
    const metaDescriptionLengthAssessment = new MetaDescriptionLengthAssessment();
    const score = metaDescriptionLengthAssessment.calculateScore(descriptionLength);
    const maximumLength = metaDescriptionLengthAssessment.getMaximumLength();

    return {
        max: maximumLength,
        actual: descriptionLength,
        score: score,
    };
}

const DescriptionProgressBar = ({description = '', date = ''}) => {
    const [descriptionProgress, setDescriptionProgress] = useState({
        progress: null,
        description: ''
    });

    useEffect(() => {
        setDescriptionProgress(prevState => {
            return {
                ...prevState, ...{
                    progress: getDescriptionProgress(description, date),
                    description: description
                }
            }
        })
    }, [description]);

    if (descriptionProgress.progress !== null) {
        return <ProgressBar max={descriptionProgress.progress.max} value={descriptionProgress.progress.actual}
                            progressColor={getProgressColor(descriptionProgress.progress.score)} />
    }
    return <></>
}

const mapStateToProps = (state) => {
    return {
        ...state.content
    }
}

export default connect(mapStateToProps)(DescriptionProgressBar);
