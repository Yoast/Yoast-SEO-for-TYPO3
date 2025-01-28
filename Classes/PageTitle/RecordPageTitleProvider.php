<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\PageTitle;

use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Record\Record;
use YoastSeoForTypo3\YoastSeo\Record\RecordService;

class RecordPageTitleProvider extends AbstractPageTitleProvider
{
    protected RecordService $recordService;

    public function __construct(?RecordService $recordService = null)
    {
        if ($recordService === null) {
            $recordService = GeneralUtility::makeInstance(RecordService::class);
        }
        $this->recordService = $recordService;
    }

    public function getTitle(): string
    {
        $activeRecord = $this->recordService->getActiveRecord();
        if (!$activeRecord instanceof Record || !$activeRecord->shouldGeneratePageTitle()) {
            return '';
        }

        $recordData = $activeRecord->getRecordData();

        $title = $recordData[$activeRecord->getTitleField()] ?? '';
        if (!isset($recordData['seo_title'])) {
            return $title;
        }

        if (!empty($recordData['seo_title'])) {
            $title = $recordData['seo_title'];
        }

        return $title;
    }
}
