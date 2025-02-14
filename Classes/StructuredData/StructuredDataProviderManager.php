<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Service\DependencyOrderingService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Cache\CacheLifetimeCalculator;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * This class will take care of the different providers and returns the title with
 * the highest priority
 */
class StructuredDataProviderManager implements SingletonInterface
{
    public function __construct(
        protected FrontendInterface $pageCache
    ) {}

    /**
     * @param array<string, mixed> $params
     */
    public function render(array &$params, object $pObj): void
    {
        if (!$this->isFrontendRequest()) {
            return;
        }

        $data = $this->getStructuredData();

        $params['headerData']['StructuredDataManager']
            = $this->getSourceComment() . PHP_EOL . $this->buildJsonLdBlob($data);
    }

    /**
     * @param array<string, array<string, mixed>> $src
     */
    protected function buildJsonLdBlob(array $src): string
    {
        $data = [];
        foreach ($src as $dataItems) {
            foreach ($dataItems as $item) {
                $data[] = $item;
            }
        }

        if (empty($data)) {
            return '';
        }

        return '<script type="application/ld+json">' . json_encode($data) . '</script>';
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getStructuredData(): array
    {
        $structuredData = [];
        foreach ($this->getOrderedStructuredDataProviders() as $provider => $configuration) {
            $cacheIdentifier = $this->getTypoScriptFrontendController()->newHash . '-structured-data-' . $provider;
            if ($this->pageCache instanceof FrontendInterface) {
                $data = $this->pageCache->get($cacheIdentifier);
                if ($data !== false) {
                    $structuredData[$provider] = $data;
                    continue;
                }
            }
            $structuredDataProviderObject = $this->getStructuredDataProviderObject($configuration);
            if ($structuredDataProviderObject === null) {
                continue;
            }

            if ($data = $structuredDataProviderObject->getData()) {
                $this->pageCache->set(
                    $cacheIdentifier,
                    $data,
                    ['pageId_' . ($this->getTypoScriptFrontendController()->page['uid'] ?? $this->getTypoScriptFrontendController()->id)],
                    $this->getCacheTimeout(),
                );
            }

            if (!empty($data)) {
                $structuredData[$provider] = $data;
            }
        }

        return $structuredData;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    protected function getStructuredDataProviderObject(array $configuration): StructuredDataProviderInterface|null
    {
        if (!class_exists($configuration['provider']) || !is_subclass_of($configuration['provider'], StructuredDataProviderInterface::class)) {
            return null;
        }

        $providerObject = GeneralUtility::makeInstance($configuration['provider']);
        if (method_exists($providerObject, 'setConfiguration')) {
            $providerObject->setConfiguration($configuration);
        }
        return $providerObject;
    }

    private function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getOrderedStructuredDataProviders(): array
    {
        $structuredDataProviders = $this->getStructuredDataProviderConfiguration();
        $structuredDataProviders = $this->setProviderOrder($structuredDataProviders);

        return GeneralUtility::makeInstance(DependencyOrderingService::class)
            ->orderByDependencies($structuredDataProviders);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getStructuredDataProviderConfiguration(): array
    {
        $typoscriptService = GeneralUtility::makeInstance(TypoScriptService::class);
        $config = $typoscriptService->convertTypoScriptArrayToPlainArray(
            $this->getTypoScriptFrontendController()->config['config'] ?? []
        );

        return $config['structuredData']['providers'] ?? [];
    }

    /**
     * @param array<string, array<string, mixed>> $orderInformation
     * @return array<string, array<string, mixed>>
     */
    protected function setProviderOrder(array $orderInformation): array
    {
        foreach ($orderInformation as $provider => &$configuration) {
            if (isset($configuration['before'])) {
                $configuration['before'] = $this->getOrderConfiguration($provider, $configuration, 'before');
            }
            if (isset($configuration['after'])) {
                $configuration['after'] = $this->getOrderConfiguration($provider, $configuration, 'after');
            }
        }
        return $orderInformation;
    }

    /**
     * @param array<string, mixed> $configuration
     * @return string[]
     */
    private function getOrderConfiguration(string $provider, array $configuration, string $key): array
    {
        if (is_string($configuration[$key])) {
            return GeneralUtility::trimExplode(',', $configuration[$key], true);
        }
        if (!is_array($configuration[$key])) {
            throw new \UnexpectedValueException(
                'The specified "' . $key . '" order configuration for provider "' . $provider . '" is invalid.',
                1551014599
            );
        }
        return $configuration[$key];
    }

    protected function isFrontendRequest(): bool
    {
        return ($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend();
    }

    protected function getSourceComment(): string
    {
        return '<!-- This site is optimized with the Yoast SEO for TYPO3 plugin - https://yoast.com/typo3-extensions-seo/ -->';
    }

    protected function getCacheTimeout(): int
    {
        if (GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() < 13) {
            return $this->getTypoScriptFrontendController()->get_cache_timeout();
        }

        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if ($request === null) {
            return 0;
        }

        $pageInformation = $request->getAttribute('frontend.page.information');
        $typoScriptConfigArray = $request->getAttribute('frontend.typoscript')->getConfigArray();
        $context = GeneralUtility::makeInstance(Context::class);

        return GeneralUtility::makeInstance(CacheLifetimeCalculator::class)
            ->calculateLifetimeForPage(
                $pageInformation->getId(),
                $pageInformation->getPageRecord(),
                $typoScriptConfigArray,
                0,
                $context
            );
    }
}
