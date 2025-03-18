import React from 'react';
import LoadingSpinner from './LoadingSpinner';
import {__, _n} from "@wordpress/i18n";
import {InsightsCard} from "@yoast/components";

const ReadingTime = ({readingTime}) => {
    if (readingTime) {
        return <InsightsCard
            amount={readingTime}
            unit={_n("minute", "minutes", readingTime, "wordpress-seo")}
            title={__("Reading time", "wordpress-seo")}
            linkTo={'https://yoa.st/4fd'}
            linkText={__("Learn more about reading time", "wordpress-seo")}
        />
    }
    return <LoadingSpinner/>
}

export default ReadingTime;