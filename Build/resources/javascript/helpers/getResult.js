const getResult = (analysis, resultType, resultSubtype) => {
    if (resultSubtype !== undefined) {
        return analysis[resultType][resultSubtype];
    } else {
        return analysis[resultType];
    }
}

export default getResult;
