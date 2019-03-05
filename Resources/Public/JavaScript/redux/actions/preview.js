export const GET_PREVIEW_REQUEST = 'GET_PREVIEW_REQUEST';
export const GET_PREVIEW_SUCCESS = 'GET_PREVIEW_SUCCESS';
export const GET_PREVIEW_ERROR = 'GET_PREVIEW_ERROR';

export function getPreview() {
    return dispatch => {
        dispatch({type: GET_PREVIEW_REQUEST});

        fetch(tx_yoast_seo.settings.preview)
            .then(response => {
                return response.json();
            })
            .then(data => {
                if (!data.description) {
                    const bodyText = document.createElement('div');
                    bodyText.innerHTML = data.body;
                    data.description = bodyText.innerText;
                }

                dispatch({type: GET_PREVIEW_SUCCESS, payload: data});
            })
            .catch(error => {
                dispatch({type: GET_PREVIEW_ERROR, payload: error, error: true});
            });
    };
}