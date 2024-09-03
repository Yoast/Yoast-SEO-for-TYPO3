<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Hooks;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\JsonConfigUtility;

class BackendYoastConfig
{
    public function renderConfig(array &$params, PageRenderer $pObject): void
    {
        if (!($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            || !ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()
        ) {
            return;
        }

        $jsonConfigUtility = GeneralUtility::makeInstance(JsonConfigUtility::class);
        $pObject->addJsInlineCode('yoast-json-config', $jsonConfigUtility->render(), true, false, true);
    }
}
