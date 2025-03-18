import React from 'react';
import {__, sprintf} from "@wordpress/i18n";
import LoadingSpinner from './LoadingSpinner';
import {DataModel} from '@yoast/components';
import {safeCreateInterpolateElement} from "../helpers/safeCreateInterpolateElement";

// See @wordpress-seo/packages/components/src/WordOccurrenceInsights.js

const getKeywordResearchArticleLink = (url) => {
    const keywordsResearchLinkTranslation = sprintf(
        __(
            "Read our %1$sultimate guide to keyword research%2$s to learn more about keyword research and keyword strategy.",
            "wordpress-seo"
        ),
        "<a>",
        "</a>"
    );

    return safeCreateInterpolateElement(keywordsResearchLinkTranslation, {
        a: <a href={url} target="_blank" rel="noreferrer"/>,
    });
};

/**
 * @summary Determine the keyword suggestion explanation.
 *
 * @param {Array} keywords The keyword suggestions that were found.
 * @returns {string} The translated text.
 */
const getExplanation = keywords => {
    if (keywords.length === 0) {
        return __(
            "Once you add a bit more copy, we'll give you a list of words that occur the most in the content. These give an indication of what your content focuses on.",
            "wordpress-seo"
        );
    }

    return __(
        "The following words occur the most in the content. These give an indication of what your content focuses on. If the words differ a lot from your topic, you might want to rewrite your content accordingly. ",
        "wordpress-seo"
    );
};

const getWords = (keywords) => {
    const words = keywords.slice(0, 20);
    words.sort((a, b) => {
        return b.occurrences - a.occurrences;
    });
    const allOccurrences = words.map(prominentWord => prominentWord.occurrences);
    const maxOccurrences = Math.max(...allOccurrences);

    return words.map(
        (word) => {
            const occurrence = word.occurrences;
            return {
                name: word.word,
                number: occurrence,
                width: (occurrence / maxOccurrences) * 100,
            };
        }
    );
}

const Insights = ({keywords}) => {
    if (keywords) {
        const words = getWords(keywords);
        return <div>
            <p className="yoast-field-group__title">{__("Prominent words", "wordpress-seo")}</p>
            <p>{getExplanation(keywords)}</p>
            {words.length > 0 && <DataModel items={words}/>}
            <p>{getKeywordResearchArticleLink('https://yoa.st/keyword-research-metabox')}</p>
        </div>
    }
    return <LoadingSpinner/>
}

export default Insights;