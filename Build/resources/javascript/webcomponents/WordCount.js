import React from 'react';
import LoadingSpinner from './LoadingSpinner';
import {__, _n} from "@wordpress/i18n";
import {InsightsCard} from "@yoast/components";

const WordCount = ({count, unit}) => {
    if (count) {
        let unitString = _n("word", "words", count, "wordpress-seo");
        let titleString = __("Word count", "wordpress-seo");
        let linkText = __("Learn more about word count", "wordpress-seo");
        if (unit === "character") {
            unitString = _n("character", "characters", count, "wordpress-seo");
            titleString = __("Character count", "wordpress-seo");
            linkText = __("Learn more about character count", "wordpress-seo");
        }

        return <InsightsCard
            amount={count}
            unit={unitString}
            title={titleString}
            //linkTo={ 'https://yoa.st/word-count' }
            linkText={linkText}
        />
    }
    return <LoadingSpinner/>
}

export default WordCount;