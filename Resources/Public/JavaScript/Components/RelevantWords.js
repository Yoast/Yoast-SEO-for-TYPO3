import React from 'react';
import { connect } from 'react-redux';

import LoadingIndicator from './LoadingIndicator';

class RelevantWords extends React.Component {

    constructor(props) {
        super(props);
    }

    render() {
        if (this.props.relevantWords) {
            let keywords = this.props.relevantWords.result.slice( 0, 5 ).map( word => word.getCombination() );

            return (
                <ol className="yoast-keyword-suggestions__list">
                { keywords.map( ( word ) => {
                        return (
                            <li key={word}
                        className="yoast-keyword-suggestions__item">
                            { word }
                            </li>
                    );
                    } ) }
                </ol>
            );
        }
        return (
            <LoadingIndicator/>
        )
    }
}

function mapStateToProps (state) {

    return {
        ...state.content,
        ...state.analysis,
        ...state.relevantWords,
        keyword: state.focusKeyword
    }
}

export default connect(mapStateToProps)(RelevantWords);
