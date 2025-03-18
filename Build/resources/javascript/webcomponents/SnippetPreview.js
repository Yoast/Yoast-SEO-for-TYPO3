import React, {useState} from 'react';
import {SnippetPreview as YoastSnippetPreview} from '@yoast/search-metadata-previews';
import ModeSwitcher from './../Components/ModeSwitcher';
import {DEFAULT_MODE} from '@yoast/search-metadata-previews/build/snippet-preview/constants';

const SnippetPreview = ({title, url, faviconSrc, wordsToHighlight, description, locale}) => {
  const [mode, setMode] = useState(DEFAULT_MODE);

  return <>
    <ModeSwitcher onChange={(newMode) => setMode(newMode)} active={mode}/>
    <YoastSnippetPreview siteName="" title={title} url={url} description={description} faviconSrc={faviconSrc}
                         wordsToHighlight={wordsToHighlight} locale={locale} mode={mode} onMouseUp={() => {}}
    />
  </>
}

export default SnippetPreview;
