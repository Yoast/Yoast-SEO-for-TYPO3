import r2wc from "@r2wc/react-to-web-component"
import Analysis from "./webcomponents/Analysis";
import DescriptionProgressBar from "./webcomponents/DescriptionProgressBar";
import SnippetPreview from "./webcomponents/SnippetPreview";
import LoadingSpinner from "./webcomponents/LoadingSpinner";
import StatusIcon from "./webcomponents/StatusIcon";
import {setLocaleData} from "@wordpress/i18n";

setLocaleData({'': {'yoast-components': {}}}, 'yoast-components');
if (window.YoastTranslations) {
    for (let translation of YoastTranslations) {
        setLocaleData(translation.locale_data['wordpress-seo'], translation.domain);
    }
} else {
    setLocaleData({'': {'wordpress-seo': {}}}, 'wordpress-seo');
}

customElements.define('yoast-loading-spinner', r2wc(LoadingSpinner));

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
