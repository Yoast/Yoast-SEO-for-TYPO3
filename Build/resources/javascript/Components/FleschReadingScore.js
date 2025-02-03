import React, {useMemo} from 'react';
import {connect} from 'react-redux';
import LoadingIndicator from './LoadingIndicator';
import { InsightsCard } from '@yoast/components';
import { DIFFICULTY } from "yoastseo";
import { __ } from '@wordpress/i18n';

// See @wordpress-seo/packages/js/src/insights/src/components/flesch-reading-ease.js

/**
 * Returns the difficulty feedback string (e.g. 'very easy')
 *
 * @param {DIFFICULTY} difficulty The Flesch reading ease difficulty.
 *
 * @returns {string} The difficulty feedback string.
 */
function getDifficultyFeedback( difficulty ) {
    switch ( difficulty ) {
        case DIFFICULTY.NO_DATA:
            return __( "no data", "wordpress-seo" );
        case DIFFICULTY.VERY_EASY:
            return __( "very easy", "wordpress-seo" );
        case DIFFICULTY.EASY:
            return __( "easy", "wordpress-seo" );
        case DIFFICULTY.FAIRLY_EASY:
            return __( "fairly easy", "wordpress-seo" );
        case DIFFICULTY.OKAY:
            return __( "okay", "wordpress-seo" );
        case DIFFICULTY.FAIRLY_DIFFICULT:
            return __( "fairly difficult", "wordpress-seo" );
        case DIFFICULTY.DIFFICULT:
            return __( "difficult", "wordpress-seo" );
        case DIFFICULTY.VERY_DIFFICULT:
            return __( "very difficult", "wordpress-seo" );
    }
}

/**
 * Returns the call to action.
 *
 * @param {DIFFICULTY} difficulty The Flesch reading ease difficulty.
 *
 * @returns {string} The call to action.
 */
function getCallToAction( difficulty ) {
    switch ( difficulty ) {
        case DIFFICULTY.FAIRLY_DIFFICULT:
        case DIFFICULTY.DIFFICULT:
        case DIFFICULTY.VERY_DIFFICULT:
            return __( "Try to make shorter sentences, using less difficult words to improve readability", "wordpress-seo" );
        case DIFFICULTY.NO_DATA:
            return __( "Continue writing to get insight into the readability of your text!", "wordpress-seo" );
        default:
            return __( "Good job!", "wordpress-seo" );
    }
}

/**
 * Generates the description given a score and difficulty.
 *
 * @param {number} score The flesch reading ease score.
 * @param {DIFFICULTY} difficulty The flesch reading ease difficulty.
 *
 * @returns {string} The description.
 */
function getDescription( score, difficulty ) {
    // A score of -1 signals that no valid FRE was calculated.
    if ( score === -1 ) {
        return sprintf(
            __(
                "Your text should be slightly longer to calculate your Flesch reading ease score.",
                "wordpress-seo"
            )
        );
    }
    return sprintf(
        __(
            "The copy scores %1$s in the test, which is considered %2$s to read.",
            "wordpress-seo"
        ),
        score,
        getDifficultyFeedback( difficulty )
    );
}

/**
 * Retrieves the description as a React element.
 *
 * @param {number} score The Flesch reading ease score.
 * @param {DIFFICULTY} difficulty The difficulty.
 * @param {string} link The link to the call to action.
 *
 * @returns {JSX.Element} The React element.
 */
function getDescriptionElement( score, difficulty, link ) {
    const callToAction = getCallToAction( difficulty );
    return <span>
        {getDescription(score, difficulty)}
        &nbsp;
        {difficulty >= DIFFICULTY.FAIRLY_DIFFICULT
            ? <a href={link} target="_blank" rel="noopener noreferrer">{callToAction + "."}</a>
            : callToAction
        }
    </span>;
}

const FleschReadingScore = ({flesch}) => {
    if (flesch) {
        let score = flesch.result.score;

        const description = useMemo(() => {
            return getDescriptionElement(score, flesch.result.difficulty, 'https://yoa.st/34s');
        })

        if ( score === -1 ) {
            score = "?";
        }

        return <InsightsCard
            amount={ score }
            unit={ __( "out of 100", "wordpress-seo" ) }
            title={ __( "Flesch reading ease", "wordpress-seo" ) }
            linkTo={ 'https://yoa.st/34r' }
            linkText={ __( "Learn more about Flesch reading ease", "wordpress-seo" ) }
            description={ description }
        />
    }
    return <LoadingIndicator />
}

const mapStateToProps = (state) => {
    return {
        ...state.flesch
    }
}

export default connect(mapStateToProps)(FleschReadingScore);
