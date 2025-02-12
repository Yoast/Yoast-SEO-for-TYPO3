import React from 'react';
import {connect} from 'react-redux';

import {FacebookPreview as YoastFacebookPreview} from '@yoast/social-metadata-previews';

const FacebookPreview = (props) => {
    return <YoastFacebookPreview {...props} />
}

function mapStateToProps(state) {
    return {
        siteUrl: state.facebookPreview.siteBase,
        title: state.facebookPreview.title,
        description: state.facebookPreview.description,
        imageUrl: state.facebookPreview.imageUrl
    }
}

export default connect(mapStateToProps)(FacebookPreview);