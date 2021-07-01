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
                // @TODO this is to show content for the snippet preview, but also causes the analysis to see this as the meta description
                if (!data.description) {
                    const bodyText = document.createElement('div');
                    bodyText.innerHTML = data.body;
                    data.description = bodyText.innerText;
                }

                dispatch({type: GET_CONTENT_SUCCESS, payload: data});
            })
            .catch(error => {
                dispatch({type: GET_CONTENT_ERROR, payload: error, error: true});
            });
    };
}

export function updateContent(content) {
    return {
        type: UPDATE_CONTENT,
        payload: content
    }
}
