import React from 'react';
import { connect } from 'react-redux';

import {TwitterPreview as YoastTwitterPreview} from '@yoast/social-metadata-previews';

class TwitterPreview extends React.Component {

    render() {
        return (
            <React.Fragment>
                <YoastTwitterPreview {...this.props} />
            </React.Fragment>
        );
    }

}

function mapStateToProps(state) {
    return {
        siteUrl: state.twitterPreview.siteBase,
        title: state.twitterPreview.title,
        description: state.twitterPreview.description,
        imageUrl: state.twitterPreview.imageUrl
    }
}

export default connect(mapStateToProps)(TwitterPreview);