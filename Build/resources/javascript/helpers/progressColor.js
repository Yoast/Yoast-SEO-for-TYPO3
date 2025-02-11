import {default as colors} from '../colors';

export default function getProgressColor( score ) {
    if ( score >= 7 ) {
        return colors.$color_good;
    }

    if ( score >= 5 ) {
        return colors.$color_ok;
    }

    return colors.$color_bad;
}
