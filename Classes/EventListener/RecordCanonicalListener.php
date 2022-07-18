<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Seo\Event\ModifyUrlForCanonicalTagEvent;
use YoastSeoForTypo3\YoastSeo\Record\Record;
use YoastSeoForTypo3\YoastSeo\Record\RecordService;

class RecordCanonicalListener
{
    protected TypoScriptFrontendController $typoScriptFrontendController;

    protected RecordService $recordService;

    public function __construct(TypoScriptFrontendController $typoScriptFrontendController = null, RecordService $recordService = null)
    {
        if ($typoScriptFrontendController === null) {
            $typoScriptFrontendController = GeneralUtility::makeInstance(TypoScriptFrontendController::class);
        }
        if ($recordService === null) {
            $recordService = GeneralUtility::makeInstance(RecordService::class);
        }

        $this->typoScriptFrontendController = $typoScriptFrontendController;
        $this->recordService = $recordService;
    }

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
            $this->typoScriptFrontendController->cObj->typoLink_URL([
                'parameter' => $canonicalLink,
                'forceAbsoluteUrl' => true,
            ])
        );
    }
}
