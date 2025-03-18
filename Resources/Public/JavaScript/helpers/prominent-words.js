import AjaxRequest from "@typo3/core/ajax/ajax-request.js";
export function saveProminentWords(url, table, pageId, language, result) {
    result.prominentWords.sort((a, b) => b.occurrences - a.occurrences);
    if (result.prominentWords.length > 25) {
        result.prominentWords = result.prominentWords.slice(0, 25);
    }
    let compressedWords = {};
    result.prominentWords.forEach((word) => {
        compressedWords[word.stem] = word.occurrences;
    });
    new AjaxRequest(url).post({
        table: table,
        uid: pageId,
        language: language,
        words: compressedWords,
    }, {
        headers: { "Content-Type": "application/json" },
    });
}
