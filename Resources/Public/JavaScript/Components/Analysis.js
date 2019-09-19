import React from 'react';
import { connect } from 'react-redux';

import LoadingIndicator from './LoadingIndicator';

import YoastContentAnalysis from 'yoast-components/composites/Plugin/ContentAnalysis/components/ContentAnalysis';
import SvgIcon from '@yoast/components'

import { getIconForScore, mapResults } from "../mapResults";
import { helpers } from "yoastseo";

function getResult(props) {
    const {analysis, resultType, resultSubtype} = props;

    if (resultSubtype !== undefined) {
        return analysis.result[resultType][resultSubtype];
    } else {
        return analysis.result[resultType];
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
                mappedResults: mapResults( getResult(this.props).results, this.props.keyword ),
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
                mappedResults: mapResults( getResult(this.props).results, this.props.keyword ),
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

        if (this.props.content.isFetching === false && this.props.analysis.isAnalyzing === false && getResult(this.props)) {
            element = (
                <React.Fragment>
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
        keyword: state.focusKeyword
    }
}

export default connect(mapStateToProps)(Analysis);
