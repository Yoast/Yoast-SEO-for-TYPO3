import {
  MappedResult,
  MappedResults,
  YoastAnalysis,
  YoastAnalysisResult,
  YoastAnalysisResultItem
} from "@yoast/yoast-seo-for-typo3/types/yoast";

function isNestedAnalysisResult(obj: YoastAnalysisResult | Record<string, YoastAnalysisResult>): obj is Record<string, YoastAnalysisResult> {
  return typeof obj === 'object' && !('score' in obj);
}

function scoreToRating(score: number): string {
  if (score === -1) return "error";
  if (score === 0) return "feedback";
  if (score <= 4) return "bad";
  if (score <= 7) return "ok";
  if (score > 7) return "good";
  return "";
}

export function getResult(analysis: YoastAnalysis, resultType: string, resultSubtype: string | null): YoastAnalysisResult | undefined {
  const result = analysis[resultType];

  if (!result) return undefined;

  if (resultSubtype !== null && isNestedAnalysisResult(result)) {
    return result[resultSubtype];
  } else if (!isNestedAnalysisResult(result)) {
    return result;
  }

  return undefined;
}

export function getScoreFromResult(analysis: YoastAnalysis, resultType: string, resultSubtype: string | null): string {
  const result = getResult(analysis, resultType, resultSubtype);
  if (result) {
    return result.score.toString();
  }
  return '';
}

export function mapResults(results: YoastAnalysisResultItem[] = [], keywordKey = ""): MappedResults {
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
  } as MappedResults);
}

function mapResult(result: YoastAnalysisResultItem, key = ""): MappedResult {
  const {score, identifier: id, text} = result;
  let rating = scoreToRating(score);
  if (rating === "ok") rating = "OK";

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

function processResult(mappedResult: MappedResult, mappedResults: MappedResults) {
  const map: Record<string, keyof MappedResults> = {
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
