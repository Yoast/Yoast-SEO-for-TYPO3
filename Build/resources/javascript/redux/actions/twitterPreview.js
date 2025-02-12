export const UPDATE_TWITTER_PREVIEW = 'UPDATE_TWITTER_PREVIEW';

export function getTwitterData(imageUrl, siteBase) {
    return dispatch => {

        let twitterTitle = getInputValue(YoastConfig.fieldSelectors.twitterTitle);
        if (twitterTitle === '') {
            twitterTitle = getInputValue(YoastConfig.fieldSelectors.pageTitle);
        }
        let twitterDescription = getInputValue(YoastConfig.fieldSelectors.twitterDescription);
        if (twitterDescription === '') {
            twitterDescription = getInputValue(YoastConfig.fieldSelectors.description);
        }

        dispatch({
            type: UPDATE_TWITTER_PREVIEW,
            payload: {
                title: twitterTitle,
                description: twitterDescription,
                siteBase: siteBase,
                imageUrl: imageUrl
            }
        });
    };
}

function getInputValue(fieldName) {
    let inputField = document.querySelector('[name="' + fieldName + '"]');
    if (inputField) {
        return inputField.value;
    }
    return '';
}

export function updateTwitterData(content) {
    return {
        type: UPDATE_TWITTER_PREVIEW,
        payload: content
    };
}