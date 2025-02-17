<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Hooks;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\Javascript\JsonConfigService;

class BackendYoastConfig
{
    /**
     * @param array<string, mixed> $params
     */
    public function renderConfig(array &$params, PageRenderer $pObject): void
    {
        if (!($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            || !ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()
        ) {
            return;
        }

        $jsonConfigUtility = GeneralUtility::makeInstance(JsonConfigService::class);
        $pObject->addJsInlineCode('yoast-json-config', $jsonConfigUtility->render(), true, false, true);
    }
}
