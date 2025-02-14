<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\MetaTag\AdvancedRobots;

use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\Page\PageInformation;
use YoastSeoForTypo3\YoastSeo\Record\Record;
use YoastSeoForTypo3\YoastSeo\Record\RecordService;

class AdvancedRobotsGenerator
{
    public function __construct(
        protected RecordService $recordService,
        protected RobotsMetaTagManager $metaTagManager,
        protected RobotsRulesGenerator $robotsRuleGenerator
    ) {}

    /**
     * @param array<string, mixed> $params
     */
    public function generate(array $params): void
    {
        $record = $this->getRecord($params);
        if ($record === null) {
            return;
        }

        $flags = $this->extractFlags($record);
        if (array_sum($flags) === 0) { // All flags are false
            return;
        }

        $this->metaTagManager->removeRobotsTag();
        $robots = $this->robotsRuleGenerator->generateRules($flags);
        $this->metaTagManager->addRobotsTag($robots);
    }

    /**
     * @param array<string, mixed> $params
     * @return array<string, mixed>|null
     */
    protected function getRecord(array $params): ?array
    {
        $activeRecord = $this->recordService->getActiveRecord();
        if ($activeRecord instanceof Record && $activeRecord->shouldGenerateRobotsTag()) {
            return $activeRecord->getRecordData();
        }
        if (isset($params['request']) && $params['request'] instanceof ServerRequest) {
            $pageInfo = $params['request']->getAttribute('frontend.page.information');
            if ($pageInfo instanceof PageInformation) {
                return $pageInfo->getPageRecord();
            }
        }
        return $params['page'] ?? null;
    }

    /**
     * @param array<string, mixed> $record
     * @return array<string, bool>
     */
    protected function extractFlags(array $record): array
    {
        return [
            'noImageIndex' => (bool)($record['tx_yoastseo_robots_noimageindex'] ?? false),
            'noArchive' => (bool)($record['tx_yoastseo_robots_noarchive'] ?? false),
            'noSnippet' => (bool)($record['tx_yoastseo_robots_nosnippet'] ?? false),
            'noIndex' => (bool)($record['no_index'] ?? false),
            'noFollow' => (bool)($record['no_follow'] ?? false),
        ];
    }
}
