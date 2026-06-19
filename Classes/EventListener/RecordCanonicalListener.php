<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Seo\Event\ModifyUrlForCanonicalTagEvent;
use YoastSeoForTypo3\YoastSeo\Record\Record;
use YoastSeoForTypo3\YoastSeo\Record\RecordService;

class RecordCanonicalListener
{
    public function __construct(
        protected RecordService $recordService
    ) {}

    public function __invoke(ModifyUrlForCanonicalTagEvent $event): void
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
            $this->getUrl($event, $canonicalLink, $activeRecord)
        );
    }

    protected function getUrl(
        ModifyUrlForCanonicalTagEvent $event,
        string $canonicalLink,
        Record $record,
    ): string {
        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $cObj->setRequest($event->getRequest());
        $cObj->start($record->getRecordData(), $record->getTableName());
        return $cObj->createUrl([
            'parameter' => $canonicalLink,
            'forceAbsoluteUrl' => true,
        ]);
    }
}
