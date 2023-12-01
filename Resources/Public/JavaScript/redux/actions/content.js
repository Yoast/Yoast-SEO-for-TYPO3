import {stripHTMLTags} from '@yoast/helpers/strings';

export const GET_CONTENT_REQUEST = 'GET_CONTENT_REQUEST';
export const GET_CONTENT_SUCCESS = 'GET_CONTENT_SUCCESS';
export const GET_CONTENT_ERROR = 'GET_CONTENT_ERROR';
export const UPDATE_CONTENT = 'UPDATE_CONTENT';

export function getContent() {
    return dispatch => {
        dispatch({type: GET_CONTENT_REQUEST});

        return fetch(YoastConfig.urls.previewUrl)
            .then(response => {
                return response.json();
            })
            .then(data => {
                dispatch({type: GET_CONTENT_SUCCESS, payload: data});
            })
            .catch(error => {
                dispatch({type: GET_CONTENT_ERROR, payload: error, error: true});
            });
    };
}

export function updateContent(content) {
    if (typeof content.title !== "undefined") {
        content.title = stripHTMLTags(content.title);
    }
    if (typeof content.description !== "undefined") {
        content.description = stripHTMLTags(content.description);
    }
    return {
        type: UPDATE_CONTENT,
        payload: content
    }
}
