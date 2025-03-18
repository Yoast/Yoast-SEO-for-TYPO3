function isNestedAnalysisResult(obj) {
    return typeof obj === 'object' && !('score' in obj);
}
function scoreToRating(score) {
    if (score === -1)
        return "error";
    if (score === 0)
        return "feedback";
    if (score <= 4)
        return "bad";
    if (score <= 7)
        return "ok";
    if (score > 7)
        return "good";
    return "";
}
export function getResult(analysis, resultType, resultSubtype) {
    const result = analysis[resultType];
    if (!result)
        return undefined;
    if (resultSubtype !== null && isNestedAnalysisResult(result)) {
        return result[resultSubtype];
    }
    else if (!isNestedAnalysisResult(result)) {
        return result;
    }
    return undefined;
}
export function getScoreFromResult(analysis, resultType, resultSubtype) {
    const result = getResult(analysis, resultType, resultSubtype);
    if (result) {
        return result.score.toString();
    }
    return '';
}
export function mapResults(results = [], keywordKey = "") {
    return results.reduce((acc, result) => {
        if (result.text) {
            const mappedResult = mapResult(result, keywordKey);
            processResult(mappedResult, acc);
        }
        return acc;
    }, {
        errorsResults: [],
        problemsResults: [],
        improvementsResults: [],
        goodResults: [],
        considerationsResults: [],
    });
}
function mapResult(result, key = "") {
    const { score, identifier: id, text } = result;
    let rating = scoreToRating(score);
    if (rating === "ok")
        rating = "OK";
    return {
        score,
        rating,
        hasMarks: false,
        marker: [],
        id,
        text,
        markerId: key ? `${key}:${id}` : id,
    };
}
function processResult(mappedResult, mappedResults) {
    const map = {
        error: "errorsResults",
        feedback: "considerationsResults",
        bad: "problemsResults",
        OK: "improvementsResults",
        good: "goodResults"
    };
    const key = map[mappedResult.rating];
    if (key) {
        mappedResults[key].push(mappedResult);
    }
    return mappedResults;
}
