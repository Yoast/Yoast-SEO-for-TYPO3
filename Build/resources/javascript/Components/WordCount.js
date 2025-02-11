import React from 'react';
import {connect} from 'react-redux';
import LoadingIndicator from './LoadingIndicator';
import { __, _n } from "@wordpress/i18n";
import { InsightsCard } from "@yoast/components";

const WordCount = ({wordCount}) => {
    if (wordCount) {
        let unitString = _n( "word", "words", wordCount.result.count, "wordpress-seo" );
        let titleString = __( "Word count", "wordpress-seo" );
        let linkText =  __( "Learn more about word count", "wordpress-seo" );
        if ( wordCount.result.unit === "character" ) {
            unitString = _n( "character", "characters", wordCount.result.count, "wordpress-seo" );
            titleString = __( "Character count", "wordpress-seo" );
            linkText =  __( "Learn more about character count", "wordpress-seo" );
        }

        return <InsightsCard
            amount={ wordCount.result.count }
            unit={ unitString }
            title={ titleString }
            //linkTo={ 'https://yoa.st/word-count' }
            linkText={ linkText }
        />
    }
    return <LoadingIndicator />
}

const mapStateToProps = (state) => {
    return {
        ...state.wordCount
    }
}

export default connect(mapStateToProps)(WordCount);
