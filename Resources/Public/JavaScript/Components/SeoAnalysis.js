import React from 'react';
import { connect } from 'react-redux';

import LoadingIndicator from './LoadingIndicator';

import YoastContentAnalysis from 'yoast-components/composites/Plugin/ContentAnalysis/components/ContentAnalysis';
import SvgIcon from 'yoast-components/composites/Plugin/Shared/components/SvgIcon'

import { getIconForScore, mapResults } from "../mapResults";
import { helpers } from "yoastseo";

class SeoAnalysis extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            mappedResults: {}
        };

        if (this.props.result.seo) {
            this.state = {
                mappedResults: mapResults( this.props.result.seo.results, this.props.keyword ),
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
        if ( this.props.result.seo !== null && this.props.result.seo !== prevProps.result.seo ) {
            this.setState( {
                mappedResults: mapResults( this.props.result.seo[""].results, this.props.keyword ),
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

        if (this.props.isFetching === false && this.props.isAnalyzing === false && this.props.result.seo[""]) {
            let score = this.props.result.seo[""].score / 10;
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
        ...state.content,
        ...state.analysis,
        keyword: state.focusKeyword

    }
}

export default connect(mapStateToProps)(SeoAnalysis);
