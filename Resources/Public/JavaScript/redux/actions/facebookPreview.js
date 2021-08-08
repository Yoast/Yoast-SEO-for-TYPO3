export const UPDATE_FACEBOOK_PREVIEW = 'UPDATE_FACEBOOK_PREVIEW';

export function getFacebookData(imageUrl, siteBase) {
    return dispatch => {

        let ogTitle = getInputValue(YoastConfig.fieldSelectors.ogTitle);
        if (ogTitle === '') {
            ogTitle = getInputValue(YoastConfig.fieldSelectors.pageTitle);
        }
        let ogDescription = getInputValue(YoastConfig.fieldSelectors.ogDescription);
        console.log(ogDescription + '?');
        if (ogDescription === '') {
            ogDescription = getInputValue(YoastConfig.fieldSelectors.description);
        }

        dispatch({
            type: UPDATE_FACEBOOK_PREVIEW,
            payload: {
                title: ogTitle,
                description: ogDescription,
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

export function updateFacebookData(content) {
    return {
        type: UPDATE_FACEBOOK_PREVIEW,
        payload: content
    };
}
