import React from 'react';

export default class LoadingIndicator extends React.Component {

    render() {
        return (
            <div className="spinner">
                <div className="bounce bounce1"></div>
                <div className="bounce bounce2"></div>
                <div className="bounce bounce3"></div>
            </div>
        )
    }
}