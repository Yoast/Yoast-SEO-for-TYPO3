<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\MetaTag\AdvancedRobots;

class RobotsRulesGenerator
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
