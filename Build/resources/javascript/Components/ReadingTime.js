import React from 'react';
import {connect} from 'react-redux';
import LoadingIndicator from './LoadingIndicator';
import { __, _n } from "@wordpress/i18n";
import { InsightsCard } from "@yoast/components";

const ReadingTime = ({readingTime}) => {
    if (readingTime) {
        const estimatedReadingTime = readingTime.result;
        return <InsightsCard
            amount={ estimatedReadingTime }
            unit={ _n( "minute", "minutes", estimatedReadingTime, "wordpress-seo" ) }
            title={ __( "Reading time", "wordpress-seo" ) }
            linkTo={ 'https://yoa.st/4fd' }
            linkText={ __( "Learn more about reading time", "wordpress-seo" ) }
        />
    }
    return <LoadingIndicator />
}

const mapStateToProps = (state) => {
    return {
        ...state.readingTime
    }
}

export default connect(mapStateToProps)(ReadingTime);
