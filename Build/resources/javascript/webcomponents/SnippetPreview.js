import React, {useState} from 'react';
import {SnippetPreview as YoastSnippetPreview} from '@yoast/search-metadata-previews';
import ModeSwitcher from './../Components/ModeSwitcher';
import {DEFAULT_MODE} from '@yoast/search-metadata-previews/build/snippet-preview/constants';
import SnippetPreviewSkeleton from "../Components/SnippetPreviewSkeleton";

const SnippetPreview = ({title, url, faviconSrc, wordsToHighlight, description, locale, siteName}) => {
  const [mode, setMode] = useState(DEFAULT_MODE);

  if (!title && !url && !description) {
    return <SnippetPreviewSkeleton/>
  }

  return <>
    <ModeSwitcher onChange={(newMode) => setMode(newMode)} active={mode}/>
    <YoastSnippetPreview siteName={siteName} title={title} url={url} description={description} faviconSrc={faviconSrc}
                         wordsToHighlight={wordsToHighlight} locale={locale} mode={mode} onMouseUp={() => {
    }}
    />
  </>
}

export default SnippetPreview;
