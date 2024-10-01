<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use TYPO3\CMS\Seo\Event\ModifyUrlForCanonicalTagEvent;
use YoastSeoForTypo3\YoastSeo\Record\Record;
use YoastSeoForTypo3\YoastSeo\Record\RecordService;

class RecordCanonicalListener
{
    public function __construct(
        protected RecordService $recordService
    ) {}

    public function setCanonical(ModifyUrlForCanonicalTagEvent $event): void
    {
        $activeRecord = $this->recordService->getActiveRecord();
        if (!$activeRecord instanceof Record) {
            return;
        }

        $canonicalLink = $activeRecord->getRecordData()['canonical_link'] ?? '';
        if (empty($canonicalLink)) {
            return;
        }

        $event->setUrl(
            $GLOBALS['TSFE']->cObj->typoLink_URL([
                'parameter' => $canonicalLink,
                'forceAbsoluteUrl' => true,
            ])
        );
    }
}
