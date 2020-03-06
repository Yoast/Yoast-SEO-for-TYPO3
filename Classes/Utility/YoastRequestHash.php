<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Utility;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class YoastReguestHash
 * @package YoastSeoForTypo3\YoastSeo\Utility
 */
class YoastRequestHash
{
    /**
     * @param $serverParams
     * @return bool
     */
    public static function isValid(ServerRequestInterface $request): bool
    {
        $serverParams = $request->getServerParams();
        $queryParams = $request->getQueryParams();

        if (isset($serverParams['HTTP_X_YOAST_PAGE_REQUEST'])) {
            $requestUrl = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');

            return $serverParams['HTTP_X_YOAST_PAGE_REQUEST'] === GeneralUtility::hmac($requestUrl);
        }

        if (isset($queryParams['xYoastPageRequest']) && isset($queryParams['uriToCheck'])) {
            return $queryParams['xYoastPageRequest'] === GeneralUtility::hmac($queryParams['uriToCheck']);
        }

        return false;
    }
}
