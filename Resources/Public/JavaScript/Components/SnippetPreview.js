import React from 'react';

import LoadingIndicator from './LoadingIndicator';

import YoastSnippetPreview from 'yoast-components/composites/Plugin/SnippetPreview/components/SnippetPreview';
import ModeSwitcher from 'yoast-components/composites/Plugin/SnippetEditor/components/ModeSwitcher';
import {DEFAULT_MODE} from 'yoast-components/composites/Plugin/SnippetPreview/constants';

export default class SnippetPreview extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            isLoading: true,
            mode: DEFAULT_MODE,
            title: props.title || '',
            description: props.description || '',
            url: props.url || '',
            keyword: tx_yoast_seo.settings.focusKeyword
        };

        fetch(tx_yoast_seo.settings.preview)
            .then(response => {
                return response.json();
            })
            .then(data => {
                const bodyText = document.createElement('div');
                bodyText.innerHTML = data.body;

                this.setState({
                    isLoading: false,
                    title: data.title,
                    description: data.description || bodyText.innerText,
                    url: data.url
                });
            });
    }

    render() {
        const config = {
            ...this.state,
            /*mode: mode,*/
            onMouseUp: () => {
            }
        }

        let element;
        if (this.state.isLoading === false) {
            element = (
                <React.Fragment>
                    <YoastSnippetPreview {...config} />
                    <ModeSwitcher onChange={(newMode) => this.setState({mode: newMode})} active={config.mode}/>
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
