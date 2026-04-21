import React from 'react';

import {FacebookPreview as YoastFacebookPreview} from '@yoast/social-metadata-previews';

const FacebookPreview = ({siteBase, title, description, imageUrl, onImageClick}) => {
    return <YoastFacebookPreview siteUrl={siteBase} title={title} description={description} imageUrl={imageUrl}
                                 onImageClick={onImageClick}/>
}

export default FacebookPreview;