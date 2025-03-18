<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Frontend;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

readonly class LegacyFrontendService implements FrontendServiceInterface
{
    public function isFrontendRequest(): bool
    {
        return ($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend();
    }

    /**
     * @return array<string, mixed>
     */
    public function getTyposcriptConfiguration(): array
    {
        return $this->getTypoScriptFrontendController()->config['config'] ?? [];
    }

    public function getPageUid(): int
    {
        return $this->getTypoScriptFrontendController()->page['uid'] ?? $this->getTypoScriptFrontendController()->id;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRootLine(): array
    {
        return $this->getTypoScriptFrontendController()->rootLine ?? [];
    }

    public function isSiteRoot(): bool
    {
        return (bool)($this->getTypoScriptFrontendController()->page['is_siteroot'] ?? false);
    }

    public function getCacheIdentifier(string $suffix): string
    {
        return $this->getTypoScriptFrontendController()->newHash . $suffix;
    }

    public function getCacheTimeout(): int
    {
        return $this->getTyposcriptFrontendController()->get_cache_timeout();
    }

    protected function getTyposcriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
