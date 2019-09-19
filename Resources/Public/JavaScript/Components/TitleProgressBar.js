import React from 'react';
import { connect } from 'react-redux';

import ProgressBar from '@yoast/components/ProgressBar';

import getProgressColor from '../helpers/progressColor';
import measureTextWidth from '../helpers/measureTextWidth';
import PageTitleWidthAssessment from 'yoastseo/src/assessments/seo/PageTitleWidthAssessment';
import {mapResults} from '../mapResults';

class TitleProgressBar extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            progress: this.getTitleProgress(this.props.title)
        }
    }

    componentDidUpdate( prevProps ) {
        if (this.props.title !== prevProps.title) {
            this.setState({
                progress: this.getTitleProgress(this.props.title)
            })
        }
    }

    getTitleProgress( title ) {
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

TitleProgressBar.defaultProps = {
    title: ''
}

function mapStateToProps (state) {

    return {
        ...state.content
    }
}

export default connect(mapStateToProps)(TitleProgressBar);
