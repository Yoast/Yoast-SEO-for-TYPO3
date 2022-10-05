<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Service\DependencyOrderingService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

/**
 * This class will take care of the different providers and returns the title with
 * the highest priority
 */
class StructuredDataProviderManager implements SingletonInterface
{
    /**
     * @var FrontendInterface
     */
    protected $pageCache;

    /**
     * @var string
     */
    protected $sourceComment = '';

    public function __construct()
    {
        $this->initCaches();

        if (YoastUtility::isPremiumInstalled()) {
            $this->sourceComment = '<!-- This site is optimized with the Yoast SEO Premium for TYPO3 plugin - https://yoast.com/typo3-extensions-seo/ -->';
        } else {
            $this->sourceComment = '<!-- This site is optimized with the Yoast SEO for TYPO3 plugin - https://yoast.com/typo3-extensions-seo/ -->';
        }
    }

    /**
     * @param array $params
     * @param object $pObj
     */
    public function render(&$params, $pObj)
    {
        if (TYPO3_MODE === 'FE') {
            $data = $this->getStructuredData();

            $params['headerData']['StructuredDataManager'] = $this->sourceComment .
                PHP_EOL . $this->buildJsonLdBlob($data);
        }
    }

    /**
     * @param array $src
     *
     * @return string
     */
    protected function buildJsonLdBlob(array $src): string
    {
        $data = [];
        foreach ($src as $provider => $dataItems) {
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
     * Get structured data
     *
     * @return array
     */
    public function getStructuredData(): array
    {
        $structuredData = [];

        $structuredDataProviders = $this->getStructuredDataProviderConfiguration();
        $structuredDataProviders = $this->setProviderOrder($structuredDataProviders);

        $orderedStructuredDataProviders = GeneralUtility::makeInstance(DependencyOrderingService::class)
            ->orderByDependencies($structuredDataProviders);

        foreach ($orderedStructuredDataProviders as $provider => $configuration) {
            $cacheIdentifier = $this->getTypoScriptFrontendController()->newHash . '-structured-data-' . $provider;
            if ($this->pageCache instanceof FrontendInterface &&
                $data = $this->pageCache->get($cacheIdentifier)
            ) {
                if (!empty($data)) {
                    $structuredData[$provider] = $data;
                }
                continue;
            }
            if (class_exists($configuration['provider'])
                && is_subclass_of($configuration['provider'], StructuredDataProviderInterface::class)) {
                /** @var StructuredDataProviderInterface $structuredDataProviderObject */
                $structuredDataProviderObject = GeneralUtility::makeInstance($configuration['provider']);
                if (method_exists($structuredDataProviderObject, 'setConfiguration')) {
                    $structuredDataProviderObject->setConfiguration($configuration);
                }

                if ($data = $structuredDataProviderObject->getData()) {
                    $this->pageCache->set(
                        $cacheIdentifier,
                        $data,
                        ['pageId_' . $this->getTypoScriptFrontendController()->page['uid']],
                        $this->getTypoScriptFrontendController()->get_cache_timeout()
                    );
                }

                if (!empty($data)) {
                    $structuredData[$provider] = $data;
                }
            }
        }

        return $structuredData;
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    private function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * Get the TypoScript configuration for pageTitleProviders
     *
     * @return array
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
     * Initializes the caching system.
     */
    protected function initCaches()
    {
        try {
            $this->pageCache = GeneralUtility::makeInstance(CacheManager::class)->getCache(
                'pages'
            );
        } catch (NoSuchCacheException $e) {
            // @ignoreException
        }
    }

    /**
     * @param array $orderInformation
     * @throws \UnexpectedValueException
     * @return string[]
     */
    protected function setProviderOrder(array $orderInformation): array
    {
        foreach ($orderInformation as $provider => &$configuration) {
            if (isset($configuration['before'])) {
                if (is_string($configuration['before'])) {
                    $configuration['before'] = GeneralUtility::trimExplode(',', $configuration['before'], true);
                } elseif (!is_array($configuration['before'])) {
                    throw new \UnexpectedValueException(
                        'The specified "before" order configuration for provider "' . $provider . '" is invalid.',
                        1551014599
                    );
                }
            }
            if (isset($configuration['after'])) {
                if (is_string($configuration['after'])) {
                    $configuration['after'] = GeneralUtility::trimExplode(',', $configuration['after'], true);
                } elseif (!is_array($configuration['after'])) {
                    throw new \UnexpectedValueException(
                        'The specified "after" order configuration for provider "' . $provider . '" is invalid.',
                        1551014604
                    );
                }
            }
        }
        return $orderInformation;
    }
}
