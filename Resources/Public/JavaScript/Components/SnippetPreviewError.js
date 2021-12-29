import React from 'react';
import { connect } from 'react-redux';

const SnippetPreviewError = ({error}) => {
    return <div className="yoast-seo-snippet-error">
        <p><strong>The server was not able to access the page to analyse your content</strong></p><p>When trying to fetch <a href={error.uriToCheck} target="_blank">{error.uriToCheck}</a>, a {error.statusCode} status code was received. Please make sure your server can access the page.</p>
    </div>
}

const mapStateToProps = (state) => {
    return {
        ...state.content,
    }
}

export default connect(mapStateToProps)(SnippetPreviewError);
