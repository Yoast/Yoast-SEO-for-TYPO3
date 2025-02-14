import React from 'react';
import { connect } from 'react-redux';

import { SvgIcon } from '@yoast/components';

import {getIconForScore, getTextForScore, getTypeLabelForScore} from "../helpers/mapResults";
import { interpreters } from "yoastseo";
import getResult from "../helpers/getResult";

const StatusIcon = ({content, analysis, resultType, resultSubtype, text}) => {
    const { scoreToRating } = interpreters;

    if (content.isFetching === false && analysis.isAnalyzing === false && getResult(analysis, resultType, resultSubtype)) {
        let score = getResult(analysis, resultType, resultSubtype).score / 10;
        let iconForScore = getIconForScore(scoreToRating(score));

        return <>
            <SvgIcon icon={iconForScore.icon} color={iconForScore.color} />{' '}
            {text === 'true' ? <><span className={`score-label-${resultType}`}>{getTypeLabelForScore(resultType)}</span> <span>{getTextForScore(scoreToRating(score))}</span></> : ''}
        </>
    } else {
        return <>
            <SvgIcon icon='circle' color='#bebebe' />{' '}
            {text === 'true' ? <span className={`score-label-${resultType}`}>{getTypeLabelForScore(resultType)}</span> : ''}
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
