import React from 'react';

import { SvgIcon } from '@yoast/components';

import {getIconForScore, getTextForScore, getTypeLabelForScore} from "../helpers/mapResults";
import { interpreters } from "yoastseo";

const StatusIcon = ({analysisDone, resultType, score, text}) => {
  const { scoreToRating } = interpreters;

  if (analysisDone) {
    let scoreRating = scoreToRating(score);
    let iconForScore = getIconForScore(scoreRating);

    return <>
      <SvgIcon icon={iconForScore.icon} color={iconForScore.color} />{' '}
      {text && <><span className={`score-label-${resultType}`}>{getTypeLabelForScore(resultType)}</span> <span>{getTextForScore(scoreRating)}</span></>}
    </>
  } else {
    return <>
      <SvgIcon icon='circle' color='#bebebe' />{' '}
      {text && <span className={`score-label-${resultType}`}>{getTypeLabelForScore(resultType)}</span>}
    </>
  }
}

export default StatusIcon;
