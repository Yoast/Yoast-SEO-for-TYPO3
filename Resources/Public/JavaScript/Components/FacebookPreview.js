import React from 'react';
import { connect } from 'react-redux';

import {FacebookPreview as YoastFacebookPreview} from '@yoast/social-metadata-previews';

class FacebookPreview extends React.Component {

    render() {
        return (
            <React.Fragment>
                <YoastFacebookPreview {...this.props} />
            </React.Fragment>
        );
    }

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