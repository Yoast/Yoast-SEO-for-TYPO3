<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\MetaTag\AdvancedRobots;

readonly class RobotsRulesGenerator
{
    /**
     * @param array<string, bool> $flags
     */
    public function generateRules(array $flags): string
    {
        $robots = [];
        if ($flags['noImageIndex']) {
            $robots[] = 'noimageindex';
        }
        if ($flags['noArchive']) {
            $robots[] = 'noarchive';
        }
        if ($flags['noSnippet']) {
            $robots[] = 'nosnippet';
        }
        $robots[] = $flags['noIndex'] ? 'noindex' : 'index';
        $robots[] = $flags['noFollow'] ? 'nofollow' : 'follow';

        return implode(',', $robots);
    }
}
