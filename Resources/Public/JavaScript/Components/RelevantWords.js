import React from 'react';
import { connect } from 'react-redux';

import LoadingIndicator from './LoadingIndicator';

class RelevantWords extends React.Component {

    constructor(props) {
        super(props);
    }

    render() {
        if (this.props.relevantWords) {
            let keywords = this.props.relevantWords.result.prominentWords.slice( 0, 20 );

            return (
                <ol className="yoast-keyword-suggestions__list">
                { keywords.map( ( word ) => {
                        return (
                            <li key={word.getStem()}
                                className="yoast-keyword-suggestions__item">
                                <strong>{ word.getStem() }</strong> ({ word.getOccurrences() })
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
