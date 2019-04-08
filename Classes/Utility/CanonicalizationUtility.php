<?php
declare(strict_types = 1);

namespace YoastSeoForTypo3\YoastSeo\Utility;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\CacheHashCalculator;

class CanonicalizationUtility
{
    /**
     * Get all params that are not needed to determine a canonicalized URL
     *
     * The format of the additionalCanonicalizedUrlParameters is:
     * $parameters = [
     *  'foo',
     *  'bar',
     *  'foo[bar]'
     * ]
     *
     * @param int $pageId Id of the page you want to get the excluded params
     * @param array $additionalCanonicalizedUrlParameters Which GET-params should stay besides the params used for cHash calculation
     *
     * @return array
     */
    public static function getParamsToExcludeForCanonicalizedUrl(int $pageId, array $additionalCanonicalizedUrlParameters = []): array
    {
        $cacheHashCalculator = GeneralUtility::makeInstance(CacheHashCalculator::class);

        $GET = ($GLOBALS['TYPO3_REQUEST'] instanceof ServerRequestInterface) ? $GLOBALS['TYPO3_REQUEST']->getQueryParams() : [];
        $GET['id'] = $pageId;

        $queryString = self::buildQueryString($GET, '&');
        $cHashArray = $cacheHashCalculator->getRelevantParameters($queryString);

        // By exploding the earlier imploded array, we get the flat array with URL params
        $urlParameters = GeneralUtility::explodeUrl2Array($queryString);

        $paramsToExclude = array_keys(
            array_diff(
                $urlParameters,
                $cHashArray
            )
        );

        return array_diff($paramsToExclude, $additionalCanonicalizedUrlParameters);
    }

    /**
     * Implodes a multidimensional array of query parameters to a string of GET parameters (eg. param[key][key2]=value2&param[key][key3]=value3)
     * and properly encodes parameter names as well as values. Spaces are encoded as %20
     *
     * @param array $parameters The (multidimensional) array of query parameters with values
     * @param string $prependCharacter If the created query string is not empty, prepend this character "?" or "&" else no prepend
     * @param bool $skipEmptyParameters If true, empty parameters (blank string, empty array, null) are removed.
     * @return string Imploded result, for example param[key][key2]=value2&param[key][key3]=value3
     * @see explodeUrl2Array()
     */
    public static function buildQueryString(array $parameters, string $prependCharacter = '', bool $skipEmptyParameters = false): string
    {
        if (empty($parameters)) {
            return '';
        }

        if ($skipEmptyParameters) {
            // This callback filters empty strings, array and null but keeps zero integers
            $parameters = self::filterRecursive(
                $parameters,
                function ($item) {
                    return $item !== '' && $item !== [] && $item !== null;
                }
            );
        }

        $queryString = http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
        $prependCharacter = $prependCharacter === '?' || $prependCharacter === '&' ? $prependCharacter : '';

        return $queryString && $prependCharacter ? $prependCharacter . $queryString : $queryString;
    }

    /**
     * Recursively filter an array
     *
     * @param array $array
     * @param callable|null $callback
     * @return array the filtered array
     * @see https://secure.php.net/manual/en/function.array-filter.php
     */
    public static function filterRecursive(array $array, callable $callback = null): array
    {
        $callback = $callback ?: function ($value) {
            return (bool)$value;
        };

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::filterRecursive($value, $callback);
            }

            if (!call_user_func($callback, $value)) {
                unset($array[$key]);
            }
        }

        return $array;
    }
}
