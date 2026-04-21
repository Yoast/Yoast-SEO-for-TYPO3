<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Hmac;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Legacy HMAC generator using GeneralUtility::hmac, can be dropped when support for TYPO3 v12 is dropped.
 */
final readonly class LegacyHmacGeneratorService implements HmacGeneratorServiceInterface
{
    public function generate(string $data): string
    {
        return GeneralUtility::hmac($data);
    }
}
