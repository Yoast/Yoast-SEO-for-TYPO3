import React, {useEffect, useState} from 'react';
import {connect} from 'react-redux';
import ProgressBar from '@yoast/components/ProgressBar';
import getProgressColor from '../helpers/progressColor';
import MetaDescriptionLengthAssessment from 'yoastseo/src/assessments/seo/MetaDescriptionLengthAssessment';

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
    const [progress, setProgress] = useState(null);
    const [currentDescription, setCurrentDescription] = useState('');

    useEffect(() => {
        if (description !== currentDescription) {
            setProgress(getDescriptionProgress(description, date));
            setCurrentDescription(description);
        }
    }, [description]);

    if (progress !== null) {
        return <ProgressBar max={progress.max} value={progress.actual}
                            progressColor={getProgressColor(progress.score)} />
    }
    return <></>
}

const mapStateToProps = (state) => {
    return {
        ...state.content
    }
}

export default connect(mapStateToProps)(DescriptionProgressBar);
