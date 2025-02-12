import {getContent} from './content';
import {setFocusKeyword} from './focusKeyword';
import {setFocusKeywordSynonyms} from './focusKeywordSynonyms';
import {analyzeData} from "./analysis";
import {getRelevantWords} from "./relevantWords";
import {getInsights} from "./insights";
import {getFleschReadingScore} from "./flesch";
import {getLinkingSuggestions} from "./linkingSuggestions";
import {getReadingTime} from "./readingTime";
import {getWordCount} from "./wordCount";
import {getFacebookData} from "./facebookPreview";
import {getTwitterData} from "./twitterPreview";

export default {
    getContent,
    setFocusKeyword,
    setFocusKeywordSynonyms,
    analyzeData,
    getRelevantWords,
    getInsights,
    getFleschReadingScore,
    getReadingTime,
    getWordCount,
    getLinkingSuggestions,
    getFacebookData,
    getTwitterData
};
