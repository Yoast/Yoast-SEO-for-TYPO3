<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Hmac;

use TYPO3\CMS\Core\Crypto\HashService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final readonly class HmacGeneratorService implements HmacGeneratorServiceInterface
{
    public function generate(string $data): string
    {
        $hashService = GeneralUtility::makeInstance(HashService::class);
        return $hashService->hmac($data, 'yoast_seo_typo3_salt');
    }
}
