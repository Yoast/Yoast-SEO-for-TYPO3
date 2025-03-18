import React, {useState} from 'react';
import {SnippetPreview as YoastSnippetPreview} from '@yoast/search-metadata-previews';
import ModeSwitcher from './../Components/ModeSwitcher';
import {DEFAULT_MODE} from '@yoast/search-metadata-previews/build/snippet-preview/constants';
import SnippetPreviewSkeleton from "../Components/SnippetPreviewSkeleton";

const SnippetPreview = ({title, url, faviconSrc, wordsToHighlight, description, locale, siteName, error}) => {
    const [mode, setMode] = useState(DEFAULT_MODE);

    if (typeof error === "object") {
        return <>
            <ModeSwitcher onChange={(newMode) => setMode(newMode)} active={mode}/>
            <div className="yoast-seo-snippet-error">
                <p><strong>The server was not able to access the page to analyse your content</strong></p><p>When trying
                to
                fetch <a href={error.url} target="_blank">{error.url}</a>, a {error.statusCode} status code
                was received. Please make sure your server can access the page.</p>
            </div>
        </>
    }

    if (!title && !url && !description) {
        return <SnippetPreviewSkeleton/>
    }

    return <>
        <ModeSwitcher onChange={(newMode) => setMode(newMode)} active={mode}/>
        <YoastSnippetPreview siteName={siteName} title={title} url={url} description={description}
                             faviconSrc={faviconSrc}
                             wordsToHighlight={wordsToHighlight} locale={locale} mode={mode} onMouseUp={() => {
        }}
        />
    </>
}

export default SnippetPreview;
