<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Service\DependencyOrderingService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\Frontend\FrontendServiceInterface;

class StructuredDataProviderManager implements SingletonInterface
{
    public function __construct(
        protected FrontendInterface $pageCache,
        protected FrontendServiceInterface $frontendService,
        protected DependencyOrderingService $dependencyOrderingService,
        protected TypoScriptService $typoScriptService,
    ) {}

    /**
     * @param array<string, mixed> $params
     */
    public function render(array &$params, object $pObj): void
    {
        if (!$this->frontendService->isFrontendRequest()) {
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
        $data = $src ? array_merge(...array_values($src)) : [];

        return $data
            ? '<script type="application/ld+json">' . json_encode($data) . '</script>'
            : '';
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getStructuredData(): array
    {
        $structuredData = [];
        foreach ($this->getOrderedStructuredDataProviders() as $provider => $configuration) {
            $cacheIdentifier = $this->frontendService->getCacheIdentifier('-structured-data-' . $provider);
            $data = $this->pageCache->get($cacheIdentifier);
            if ($data !== false) {
                $structuredData[$provider] = $data;
                continue;
            }
            $structuredDataProviderObject = $this->getStructuredDataProviderObject($configuration);
            if ($structuredDataProviderObject === null) {
                continue;
            }

            if ($data = $structuredDataProviderObject->getData()) {
                $this->pageCache->set(
                    $cacheIdentifier,
                    $data,
                    ['pageId_' . $this->frontendService->getPageUid()],
                    $this->frontendService->getCacheTimeout(),
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

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getOrderedStructuredDataProviders(): array
    {
        $structuredDataProviders = $this->getStructuredDataProviderConfiguration();
        $structuredDataProviders = $this->setProviderOrder($structuredDataProviders);

        return $this->dependencyOrderingService->orderByDependencies($structuredDataProviders);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getStructuredDataProviderConfiguration(): array
    {
        $config = $this->typoScriptService->convertTypoScriptArrayToPlainArray(
            $this->frontendService->getTyposcriptConfiguration()
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

    protected function getSourceComment(): string
    {
        return '<!-- This site is optimized with the Yoast SEO for TYPO3 plugin - https://yoast.com/typo3-extensions-seo/ -->';
    }
}
