import React from 'react';
import {connect} from 'react-redux';
import LoadingIndicator from './LoadingIndicator';

const Insights = ({insights}) => {
    if (insights) {
        let keywords = insights.result.slice(0, 20);
        return (
            <ol className="yoast-keyword-suggestions__list">
                {keywords.map((word) => {
                    return (
                        <li key={word.getStem()}
                            className="yoast-keyword-suggestions__item">
                            <strong>{word.getStem()}</strong> ({word.getOccurrences()})
                        </li>
                    );
                })}
            </ol>
        );
    }
    return <LoadingIndicator />
}

const mapStateToProps = (state) => {
    return {
        ...state.insights
    }
}

export default connect(mapStateToProps)(Insights);
