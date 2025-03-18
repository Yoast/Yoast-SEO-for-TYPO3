<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\Hmac\HmacGeneratorServiceInterface;

class YoastRequestService
{
    public function __construct(
        protected HmacGeneratorServiceInterface $hmacService
    ) {}

    /**
     * @param array<string, mixed> $serverParams
     */
    public function isValidRequest(array $serverParams): bool
    {
        return isset($serverParams['HTTP_X_YOAST_PAGE_REQUEST'])
            && $serverParams['HTTP_X_YOAST_PAGE_REQUEST'] === $this->hmacService->generate(
                GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL')
            );
    }
}
