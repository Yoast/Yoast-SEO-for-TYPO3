import React from 'react';
import { connect } from 'react-redux';

import ProgressBar from 'yoast-components/composites/Plugin/SnippetPreview/components/ProgressBar';

import getProgressColor from '../helpers/progressColor';
import {mapResults} from '../mapResults';
import MetaDescriptionLengthAssessment from 'yoastseo/src/assessments/seo/MetaDescriptionLengthAssessment';

class DescriptionProgressBar extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            progress: this.getDescriptionProgress(this.props.description, this.props.date)
        }
    }

    componentDidUpdate( prevProps ) {
        if (this.props.description !== prevProps.description) {
            this.setState({
                progress: this.getDescriptionProgress(this.props.description, this.props.date)
            });
        }
    }

    /**
     * Gets the description progress.
     *
     * @param {string} description The description.
     * @param {string} date        The meta description date
     *
     * @returns {Object} The description progress.
     */
    getDescriptionProgress( description, date ) {
        let descriptionLength = description.length;
        /* If the meta description is preceded by a date, two spaces and a hyphen (" - ") are added as well. Therefore,
        three needs to be added to the total length. */
        if ( date !== "" && descriptionLength > 0 ) {
            descriptionLength += date.length + 3;
        }
        const metaDescriptionLengthAssessment = new MetaDescriptionLengthAssessment();
        const score = metaDescriptionLengthAssessment.calculateScore( descriptionLength );
        const maximumLength = metaDescriptionLengthAssessment.getMaximumLength();

        return {
            max: maximumLength,
            actual: descriptionLength,
            score: score,
        };
    }

    render () {
        const {progress} = this.state;

        return (
            <ProgressBar
                max={ progress.max }
                value={ progress.actual }
                progressColor={ getProgressColor( progress.score ) }
            />
        )
    }
}

DescriptionProgressBar.defaultProps = {
    description: '',
    date: ''
}

function mapStateToProps (state) {

    return {
        ...state.content
    }
}

export default connect(mapStateToProps)(DescriptionProgressBar);