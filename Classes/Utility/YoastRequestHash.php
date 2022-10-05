<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class YoastReguestHash
 */
class YoastRequestHash
{
    /**
     * @param array $serverParams
     * @return bool
     */
    public static function isValid(array $serverParams): bool
    {
        return isset($serverParams['HTTP_X_YOAST_PAGE_REQUEST'])
            && $serverParams['HTTP_X_YOAST_PAGE_REQUEST'] === GeneralUtility::hmac(
                GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL')
            );
    }
}
