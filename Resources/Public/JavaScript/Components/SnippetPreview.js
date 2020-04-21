import React from 'react';
import { connect } from 'react-redux';

import LoadingIndicator from './LoadingIndicator';
import SnippetPreviewError from './SnippetPreviewError';

import YoastSnippetPreview from '@yoast/search-metadata-previews/snippet-preview/SnippetPreview';
import ModeSwitcher from '@yoast/search-metadata-previews/snippet-editor/ModeSwitcher';
import {DEFAULT_MODE} from '@yoast/search-metadata-previews/snippet-preview/constants';

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
            if (typeof this.props.title === 'undefined') {
                element = <SnippetPreviewError/>
            } else {
                element = (
                    <React.Fragment>
                        <ModeSwitcher onChange={(newMode) => this.setState({mode: newMode})} active={this.state.mode}/>
                        <YoastSnippetPreview {...this.props} mode={this.state.mode} onMouseUp={() => {}} />
                    </React.Fragment>
                );
            }
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
        title: state.content.title,
        faviconSrc: state.content.faviconSrc,
        wordsToHighlight: [state.focusKeyword]
    }
}

export default connect(mapStateToProps)(SnippetPreview);
