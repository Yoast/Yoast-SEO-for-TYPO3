<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\MetaTag;

use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageInformation;
use YoastSeoForTypo3\YoastSeo\Record\Record;
use YoastSeoForTypo3\YoastSeo\Record\RecordService;

class AdvancedRobotsGenerator
{
    protected RecordService $recordService;

    public function __construct(RecordService $recordService = null)
    {
        if ($recordService === null) {
            $recordService = GeneralUtility::makeInstance(RecordService::class);
        }
        $this->recordService = $recordService;
    }

    public function generate(array $params): void
    {
        $activeRecord = $this->recordService->getActiveRecord();
        if ($activeRecord instanceof Record && $activeRecord->shouldGenerateRobotsTag()) {
            $record = $activeRecord->getRecordData();
        } elseif (isset($params['request']) && $params['request'] instanceof ServerRequest && $params['request']->getAttribute('frontend.page.information') instanceof PageInformation) {
            $record = $params['request']->getAttribute('frontend.page.information')->getPageRecord();
        } else {
            $record = $params['page'] ?? null;
        }

        if ($record === null) {
            return;
        }

        $noImageIndex = (bool)($record['tx_yoastseo_robots_noimageindex'] ?? false);
        $noArchive = (bool)($record['tx_yoastseo_robots_noarchive'] ?? false);
        $noSnippet = (bool)($record['tx_yoastseo_robots_nosnippet'] ?? false);
        $noIndex = (bool)($record['no_index'] ?? false);
        $noFollow = (bool)($record['no_follow'] ?? false);

        if ($noImageIndex || $noArchive || $noSnippet || $noIndex || $noFollow) {
            $metaTagManagerRegistry = GeneralUtility::makeInstance(MetaTagManagerRegistry::class);
            $manager = $metaTagManagerRegistry->getManagerForProperty('robots');
            $manager->removeProperty('robots');

            $robots = [];
            if ($noImageIndex) {
                $robots[] = 'noimageindex';
            }
            if ($noArchive) {
                $robots[] = 'noarchive';
            }
            if ($noSnippet) {
                $robots[] = 'nosnippet';
            }
            $robots[] = $noIndex ? 'noindex' : 'index';
            $robots[] = $noFollow ? 'nofollow' : 'follow';

            $manager->addProperty('robots', implode(',', $robots));
        }
    }
}
