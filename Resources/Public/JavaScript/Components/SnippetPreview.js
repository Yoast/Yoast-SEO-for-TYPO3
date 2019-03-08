import React from 'react';
import { connect } from 'react-redux';

import LoadingIndicator from './LoadingIndicator';

import YoastSnippetPreview from 'yoast-components/composites/Plugin/SnippetPreview/components/SnippetPreview';
import ModeSwitcher from 'yoast-components/composites/Plugin/SnippetEditor/components/ModeSwitcher';
import {DEFAULT_MODE} from 'yoast-components/composites/Plugin/SnippetPreview/constants';

class SnippetPreview extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            mode: DEFAULT_MODE
        }
    }

    render() {

        let element;
        if (this.props.isFetching === false) {
            element = (
                <React.Fragment>
                    <YoastSnippetPreview {...this.props} mode={this.state.mode} onMouseUp={() => {}} />
                    <ModeSwitcher onChange={(newMode) => this.setState({mode: newMode})} active={this.state.mode}/>
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
        wordsToHighlight: [state.focusKeyword]
    }
}

export default connect(mapStateToProps)(SnippetPreview);
