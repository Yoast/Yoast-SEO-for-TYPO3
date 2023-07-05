<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\MetaTag;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Record\Record;
use YoastSeoForTypo3\YoastSeo\Record\RecordService;

class RecordMetaTagGenerator
{
    protected RecordService $recordService;

    public function __construct(RecordService $recordService = null)
    {
        if ($recordService === null) {
            $recordService = GeneralUtility::makeInstance(RecordService::class);
        }
        $this->recordService = $recordService;
    }

    public function generate(): void
    {
        $activeRecord = $this->recordService->getActiveRecord();
        if (!$activeRecord instanceof Record || !$activeRecord->shouldGenerateMetaTags()) {
            return;
        }

        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['recordMetaTags'] ?? [] as $generatorClass) {
            GeneralUtility::makeInstance($generatorClass)->generate($activeRecord);
        }
    }
}
