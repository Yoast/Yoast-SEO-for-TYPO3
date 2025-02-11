<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class YoastRequestService
{
    /**
     * @param array<string, mixed> $serverParams
     */
    public function isValidRequest(array $serverParams): bool
    {
        return isset($serverParams['HTTP_X_YOAST_PAGE_REQUEST'])
            && $serverParams['HTTP_X_YOAST_PAGE_REQUEST'] === GeneralUtility::hmac(
                GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL')
            );
    }
}
