<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Hooks;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\JsonConfigUtility;

class YoastConfigInlineJs
{
    public function renderJsonConfig(array &$params, PageRenderer $pObject): void
    {
        $jsonConfigUtility = GeneralUtility::makeInstance(JsonConfigUtility::class);
        $pObject->addJsInlineCode('yoast-json-config', $jsonConfigUtility->render());
    }
}
