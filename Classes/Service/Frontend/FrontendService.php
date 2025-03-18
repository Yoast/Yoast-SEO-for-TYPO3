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
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Cache\CacheLifetimeCalculator;
use TYPO3\CMS\Frontend\Page\PageInformation;

readonly class FrontendService implements FrontendServiceInterface
{
    public function __construct(
        #[Autowire(service: 'cache.runtime')]
        private FrontendInterface $runtimeCache,
    ) {}

    public function isFrontendRequest(): bool
    {
        return $this->getRequest() && ApplicationType::fromRequest($this->getRequest())->isFrontend();
    }

    /**
     * @return array<string, mixed>
     */
    public function getTyposcriptConfiguration(): array
    {
        return $this->getRequest()?->getAttribute('frontend.typoscript')?->getConfigArray() ?? [];
    }

    public function getPageUid(): int
    {
        return $this->getPageInformation()?->getId() ?? 0;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRootLine(): array
    {
        return $this->getPageInformation()?->getRootLine() ?? [];
    }

    public function isSiteRoot(): bool
    {
        return (bool)($this->getPageInformation()?->getPageRecord()['is_siteroot'] ?? false);
    }

    public function getCacheIdentifier(string $suffix): string
    {
        return $this->runtimeCache->get(FrontendServiceInterface::CACHE_IDENTIFIER) . $suffix;
    }

    public function getCacheTimeout(): int
    {
        if ($this->getRequest() === null || $this->getPageInformation() === null) {
            return 0;
        }

        $arguments = [
            'pageId' => $this->getPageInformation()->getId(),
            'pageRecord' => $this->getPageInformation()->getPageRecord(),
            'renderingInstructions' => $this->getTyposcriptConfiguration(),
            'context' => GeneralUtility::makeInstance(Context::class),
        ];

        if ((new Typo3Version())->getMajorVersion() === 13) {
            $arguments['defaultCacheTimoutInSeconds'] = 0;
        }

        return GeneralUtility::makeInstance(CacheLifetimeCalculator::class)
            ->calculateLifetimeForPage(...$arguments);
    }

    protected function getPageInformation(): ?PageInformation
    {
        return $this->getRequest()?->getAttribute('frontend.page.information');
    }

    protected function getRequest(): ?ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? null;
    }
}
