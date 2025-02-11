import React, {useState} from 'react';
import { connect } from 'react-redux';
import LoadingIndicator from './LoadingIndicator';
import SnippetPreviewError from './SnippetPreviewError';
import { SnippetPreview as YoastSnippetPreview } from '@yoast/search-metadata-previews';
import ModeSwitcher from './ModeSwitcher';
import {DEFAULT_MODE} from '@yoast/search-metadata-previews/build/snippet-preview/constants';

const SnippetPreview = ({isFetching, title, url, faviconSrc, wordsToHighlight, description, locale}) => {
    const [mode, setMode] = useState(DEFAULT_MODE);

    if (isFetching === false) {
        if (typeof title === 'undefined') {
            return <SnippetPreviewError/>
        }
        return <>
            <ModeSwitcher onChange={(newMode) => setMode(newMode)} active={mode}/>
            <YoastSnippetPreview siteName="" title={title} url={url} description={description} faviconSrc={faviconSrc} wordsToHighlight={wordsToHighlight} locale={locale} mode={mode} onMouseUp={() => {}} />
        </>
    }
    return <LoadingIndicator />
}

const mapStateToProps = (state) => {
    return {
        ...state.content,
        ...state.analysis,
        title: state.content.title,
        faviconSrc: state.content.faviconSrc,
        wordsToHighlight: [state.focusKeyword]
    }
}

export default connect(mapStateToProps)(SnippetPreview);
