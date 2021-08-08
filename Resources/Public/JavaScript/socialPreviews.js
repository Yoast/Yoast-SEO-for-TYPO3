import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import { debounce} from 'lodash';

import store from './redux/store';
import {getFacebookData, updateFacebookData} from "./redux/actions/facebookPreview";
import {getTwitterData, updateTwitterData} from "./redux/actions/twitterPreview";

import FacebookPreview from "./Components/FacebookPreview";
import TwitterPreview from "./Components/TwitterPreview";

const socialPreviews = [{
    socialType: 'facebook',
    component: <FacebookPreview />,
    socialDataRetrieve: function (imageUrl, siteBase) {
        return getFacebookData(imageUrl, siteBase);
    },
    socialDataUpdate: function (payload) {
        return updateFacebookData(payload);
    },
    updateFields: {
        title: YoastConfig.fieldSelectors.ogTitle,
        description: YoastConfig.fieldSelectors.ogDescription
    }
}, {
    socialType: 'twitter',
    component: <TwitterPreview />,
    socialDataRetrieve: function (imageUrl, siteBase) {
        return getTwitterData(imageUrl, siteBase);
    },
    socialDataUpdate: function (payload) {
        return updateTwitterData(payload);
    },
    updateFields: {
        title: YoastConfig.fieldSelectors.twitterTitle,
        description: YoastConfig.fieldSelectors.twitterDescription
    }
}];

socialPreviews.forEach(item => {
    let previewElement = document.querySelector('[data-yoast-social-preview-type="' + item.socialType + '"]');
    if (previewElement) {
        let imageUrl = previewElement.dataset.yoastSocialPreviewImage,
            siteBase = previewElement.dataset.yoastSocialPreviewBase;

        store.dispatch(item.socialDataRetrieve(imageUrl, siteBase));
        ReactDOM.render(<Provider store={store}>{item.component}</Provider>, previewElement);

        for (let field in item.updateFields) {
            if (item.updateFields.hasOwnProperty(field)) {
                let fieldElement = document.querySelector('[data-formengine-input-name="' + item.updateFields[field] + '"]');
                fieldElement.addEventListener('input', debounce(_ => {
                    console.log(field + ' - ' + fieldElement.value);
                    store.dispatch(item.socialDataUpdate({[field]: fieldElement.value}));
                }));
            }
        }
    }
});