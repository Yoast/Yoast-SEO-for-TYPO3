const getResult = (analysis, resultType, resultSubtype) => {
    if (resultSubtype !== undefined) {
        return analysis.result[resultType][resultSubtype];
    } else {
        return analysis.result[resultType];
    }
}

export default getResult;
