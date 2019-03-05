import { createStore, applyMiddleware, combineReducers } from 'redux';
import thunk from 'redux-thunk';
import reducers from './reducers';

const store = createStore(
    combineReducers(reducers),
    applyMiddleware(thunk)
);

const element = document.createElement('div');
document.body.appendChild(element);

export default store;