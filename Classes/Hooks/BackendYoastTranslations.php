<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Hooks;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\Javascript\JsonTranslationsService;

readonly class BackendYoastTranslations
{
    /**
     * @param array<string, mixed> $params
     */
    public function renderTranslations(array &$params, PageRenderer $pObject): void
    {
        if (!$this->isBackendRequest()) {
            return;
        }

        $jsonTranslationsService = GeneralUtility::makeInstance(JsonTranslationsService::class);
        $pObject->addJsInlineCode('yoast-translations', $jsonTranslationsService->render(), true, false, true);
    }

    protected function isBackendRequest(): bool
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        return $request instanceof ServerRequestInterface
            && ApplicationType::fromRequest($request)->isBackend();
    }
}
