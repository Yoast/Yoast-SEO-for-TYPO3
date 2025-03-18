import r2wc from "@r2wc/react-to-web-component"
import Analysis from "./webcomponents/Analysis";
import DescriptionProgressBar from "./webcomponents/DescriptionProgressBar";
import TitleProgressBar from "./webcomponents/TitleProgressBar";
import SnippetPreview from "./webcomponents/SnippetPreview";
import LoadingSpinner from "./webcomponents/LoadingSpinner";
import StatusIcon from "./webcomponents/StatusIcon";
import Insights from "./webcomponents/Insights";
import FleschReadingScore from "./webcomponents/FleschReadingScore";
import ReadingTime from "./webcomponents/ReadingTime";
import WordCount from "./webcomponents/WordCount";
import LinkingSuggestions from "./webcomponents/LinkingSuggestions";
import FacebookPreview from "./webcomponents/FacebookPreview";
import TwitterPreview from "./webcomponents/TwitterPreview";
import {setLocaleData} from "@wordpress/i18n";

setLocaleData({'': {'yoast-components': {}}}, 'yoast-components');
if (window.YoastTranslations) {
    for (let translation of YoastTranslations) {
        setLocaleData(translation.locale_data['wordpress-seo'], translation.domain);
        setLocaleData(translation.locale_data['wordpress-seo'], 'yoast-components');
    }
} else {
    setLocaleData({'': {'wordpress-seo': {}}}, 'wordpress-seo');
}

customElements.define('yoast-loading-spinner', r2wc(LoadingSpinner));

customElements.define('yoast-title-progress-bar', r2wc(TitleProgressBar, {
    props: {
        title: "string",
    }
}));

customElements.define('yoast-description-progress-bar', r2wc(DescriptionProgressBar, {
    props: {
        description: "string",
        date: "string",
    }
}));

customElements.define('yoast-snippet-preview', r2wc(SnippetPreview, {
    props: {
        title: "string",
        url: "string",
        description: "string",
        faviconSrc: "string",
        locale: "string",
        wordsToHighlight: "json",
        siteName: "string",
        error: "json",
    }
}));

customElements.define('yoast-status-icon', r2wc(StatusIcon, {
    props: {
        analysisDone: "boolean",
        resultType: "string",
        text: "boolean",
        score: "number",
    }
}))


customElements.define('yoast-analysis-result', r2wc(Analysis, {
    props: {
        analysis: "json",
    }
}))

customElements.define('yoast-insights', r2wc(Insights, {
    props: {
        keywords: "json",
    }
}))

customElements.define('yoast-flesch-reading-score', r2wc(FleschReadingScore, {
    props: {
        score: "number",
        difficulty: "number",
        unsupportedLanguage: "boolean"
    }
}));

customElements.define('yoast-reading-time', r2wc(ReadingTime, {
    props: {
        readingTime: "number",
    }
}))

customElements.define('yoast-word-count', r2wc(WordCount, {
    props: {
        count: "number",
        unit: "string"
    }
}))

customElements.define('yoast-linking-suggestions', r2wc(LinkingSuggestions, {
    props: {
        links: "json",
        isChecking: "boolean",
    }
}))

customElements.define('yoast-facebook-preview', r2wc(FacebookPreview, {
    props: {
        siteBase: "string",
        title: "string",
        description: "string",
        imageUrl: "string",
        onImageClick: "method",
    }
}));

customElements.define('yoast-twitter-preview', r2wc(TwitterPreview, {
    props: {
        siteBase: "string",
        title: "string",
        description: "string",
        imageUrl: "string",
        isLarge: "boolean",
        onImageClick: "method",
    }
}));
