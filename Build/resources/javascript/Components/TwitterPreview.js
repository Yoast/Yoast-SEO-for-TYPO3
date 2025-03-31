import React from 'react';
import { connect } from 'react-redux';

import {TwitterPreview as YoastTwitterPreview} from '@yoast/social-metadata-previews';

const TwitterPreview = (props) => {
    return <YoastTwitterPreview {...props} />
}

function mapStateToProps(state) {
    return {
        siteUrl: state.twitterPreview.siteBase,
        title: state.twitterPreview.title,
        description: state.twitterPreview.description,
        imageUrl: state.twitterPreview.imageUrl,
        isLarge: state.twitterPreview.isLarge,
    }
}

export default connect(mapStateToProps)(TwitterPreview);
