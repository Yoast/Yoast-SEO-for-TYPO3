import React from 'react';
import {connect} from 'react-redux';
import LoadingIndicator from './LoadingIndicator';
import { WordOccurrenceInsights } from '@yoast/components';

const Insights = ({insights}) => {
    if (insights) {
        let keywords = insights.result.slice(0, 20);
        return <WordOccurrenceInsights words={keywords} researchArticleLink={'https://yoa.st/keyword-research-metabox'} />
    }
    return <LoadingIndicator />
}

const mapStateToProps = (state) => {
    return {
        ...state.insights
    }
}

export default connect(mapStateToProps)(Insights);
