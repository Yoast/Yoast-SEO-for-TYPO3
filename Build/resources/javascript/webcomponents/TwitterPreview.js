import React from 'react';

import {TwitterPreview as YoastTwitterPreview} from '@yoast/social-metadata-previews';

const TwitterPreview = ({siteBase, title, description, imageUrl, isLarge, onImageClick}) => {
    return <YoastTwitterPreview siteUrl={siteBase} title={title} description={description} imageUrl={imageUrl}
                                isLarge={isLarge}
                                onImageClick={onImageClick}/>
}

export default TwitterPreview;