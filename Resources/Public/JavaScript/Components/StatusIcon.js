import React from 'react';
import { connect } from 'react-redux';

import SvgIcon from '@yoast/components/SvgIcon'

import { getIconForScore, getTextForScore } from "../mapResults";
import { helpers } from "yoastseo";
import getResult from "../getResult";

const StatusIcon = ({content, analysis, resultType, resultSubtype, text}) => {
    const { scoreToRating } = helpers;

    if (content.isFetching === false && analysis.isAnalyzing === false && getResult(analysis, resultType, resultSubtype)) {
        let score = getResult(analysis, resultType, resultSubtype).score / 10;
        let iconForScore = getIconForScore(scoreToRating(score));

        return <>
            <SvgIcon icon={iconForScore.icon} color={iconForScore.color} />{' '}
            {text === 'true' ? getTextForScore(resultType, scoreToRating(score)) : ''}
        </>
    } else {
        return <>
            <SvgIcon icon='circle' color='#bebebe' />{' '}
            {text === 'true' ? getTextForScore(resultType, '') : ''}
        </>
    }
}

const mapStateToProps = (state) => {
    return {
        content: state.content,
        analysis: state.analysis
    }
}

export default connect(mapStateToProps)(StatusIcon);
