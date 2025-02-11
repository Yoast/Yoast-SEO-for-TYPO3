<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\PageTitle;

use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;
use YoastSeoForTypo3\YoastSeo\Record\Record;
use YoastSeoForTypo3\YoastSeo\Record\RecordService;

class RecordPageTitleProvider extends AbstractPageTitleProvider
{
    public function __construct(
        protected RecordService $recordService
    ) {}

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
