<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\MetaTag;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\MetaTag\Generator\GeneratorInterface;
use YoastSeoForTypo3\YoastSeo\Record\Record;
use YoastSeoForTypo3\YoastSeo\Record\RecordService;

class RecordMetaTagGenerator
{
    public function __construct(
        protected RecordService $recordService
    ) {}

    public function generate(): void
    {
        $activeRecord = $this->recordService->getActiveRecord();
        if (!$activeRecord instanceof Record || !$activeRecord->shouldGenerateMetaTags()) {
            return;
        }

        /** @var class-string $generatorClass */
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['recordMetaTags'] ?? [] as $generatorClass) {
            $generator = GeneralUtility::makeInstance($generatorClass);
            if ($generator instanceof GeneratorInterface) {
                $generator->generate($activeRecord);
            }
        }
    }
}
