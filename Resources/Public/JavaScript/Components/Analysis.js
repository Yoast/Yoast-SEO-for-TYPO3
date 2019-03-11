import React from 'react';
import { connect } from 'react-redux';

import LoadingIndicator from './LoadingIndicator';

import YoastContentAnalysis from 'yoast-components/composites/Plugin/ContentAnalysis/components/ContentAnalysis';
import SvgIcon from 'yoast-components/composites/Plugin/Shared/components/SvgIcon'

import { getIconForScore, mapResults } from "../mapResults";
import { helpers } from "yoastseo";

function getResults(props) {
    const {analysis, resultType, resultSubtype} = props;

    if (resultSubtype !== undefined) {
        return analysis.result[resultType][resultSubtype].results;
    } else {
        return analysis.result[resultType].results;
    }
}

class Analysis extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            mappedResults: {}
        };

        if (this.props.analysis.result[this.props.resultType]) {
            this.state = {
                mappedResults: mapResults( getResults(this.props), this.props.keyword ),
            };
        }
    }

    /**
     * If there are new analysis results, map them to their corresponding collapsible
     * (error, problem, consideration, improvement, good).
     *
     * If the results are null, we assume the analysis is still being performed.
     *
     * @param {object} prevProps The previous props.
     *
     * @returns {void}
     */
    componentDidUpdate( prevProps ) {
        if ( this.props.analysis.result[this.props.resultType] !== null && this.props.analysis.result[this.props.resultType] !== prevProps.analysis.result[this.props.resultType] ) {
            this.setState( {
                mappedResults: mapResults( getResults(this.props), this.props.keyword ),
            } );
        }
    }

    render() {
        const { scoreToRating } = helpers;
        const { mappedResults } = this.state;
        const {
            errorsResults,
            improvementsResults,
            goodResults,
            considerationsResults,
            problemsResults,
        } = mappedResults;
        const marksButtonStatus = 'disabled';

        let element;

        if (this.props.content.isFetching === false && this.props.analysis.isAnalyzing === false && getResults(this.props)) {
            let score = getResults(this.props).score / 10;
            let iconForScore = getIconForScore(scoreToRating(score));

            element = (
                <React.Fragment>
                    <SvgIcon icon={ iconForScore.icon } color={ iconForScore.color } />
                    <YoastContentAnalysis
                        problemsResults={ problemsResults }
                        improvementsResults={ improvementsResults }
                        goodResults={ goodResults }
                        considerationsResults={ considerationsResults }
                        errorsResults={ errorsResults }
                        marksButtonStatus= { marksButtonStatus }
                    />
                </React.Fragment>
            );
        } else {
            element = <LoadingIndicator/>
        }

        return (
            <React.Fragment>
                {element}
            </React.Fragment>
        );
    }
}

function mapStateToProps (state) {

    return {
        content: state.content,
        analysis: state.analysis,
        cornerstoneContent: state.cornerstoneContent,
        keyword: state.focusKeyword
    }
}

export default connect(mapStateToProps)(Analysis);
