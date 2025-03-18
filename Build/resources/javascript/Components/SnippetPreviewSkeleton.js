import ModeSwitcher from './../Components/ModeSwitcher';
import {DEFAULT_MODE} from '@yoast/search-metadata-previews/build/snippet-preview/constants';

const SnippetPreviewSkeleton = () => {
  return <>
    <ModeSwitcher active={DEFAULT_MODE}/>
    <div className="yoast-snippet-preview-skeleton">
      <div className="yoast-snippet-preview-skeleton-header">
        <div className="yoast-skeleton yoast-skeleton-favicon"/>
        <div className="yoast-skeleton yoast-skeleton-website"/>
      </div>
      <div className="yoast-skeleton yoast-skeleton-title"/>
      <div className="yoast-skeleton yoast-skeleton-description"/>
    </div>
  </>
}

export default SnippetPreviewSkeleton;
