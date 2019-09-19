import React from 'react';
import { connect } from 'react-redux';

import SvgIcon from '@yoast/components/SvgIcon'

import { getIconForScore, getTextForScore } from "../mapResults";
import { helpers } from "yoastseo";

function getResult(props) {
    const {analysis, resultType, resultSubtype} = props;

    if (resultSubtype !== undefined) {
        return analysis.result[resultType][resultSubtype];
    } else {
        return analysis.result[resultType];
    }
}

class StatusIcon extends React.Component {

    constructor(props) {
        super(props);
    }

    render() {
        const { scoreToRating } = helpers;
        let element;

        let altText = '';

        if (this.props.content.isFetching === false && this.props.analysis.isAnalyzing === false && getResult(this.props)) {
            let score = getResult(this.props).score / 10;
            let iconForScore = getIconForScore(scoreToRating(score));

            if (this.props.text == 'true') {
                altText = getTextForScore(this.props.resultType, scoreToRating(score))
            }

            element = (
                <React.Fragment>
                    <SvgIcon icon={ iconForScore.icon } color={ iconForScore.color } /> {altText}
                </React.Fragment>
            );
        } else {
            if (this.props.text == 'true') {
                altText = getTextForScore(this.props.resultType, '')
            }

            element = (
                <React.Fragment>
                    <SvgIcon icon='circle' color='#bebebe' /> {altText}
                </React.Fragment>
            );
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
        analysis: state.analysis
    }
}

export default connect(mapStateToProps)(StatusIcon);
