<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Service\DependencyOrderingService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * This class will take care of the different providers and returns the title with
 * the highest priority
 */
class StructuredDataProviderManager implements SingletonInterface
{
    protected FrontendInterface $pageCache;
    protected string $sourceComment = '';

    public function __construct()
    {
        $this->initCaches();
        $this->sourceComment = '<!-- This site is optimized with the Yoast SEO for TYPO3 plugin - https://yoast.com/typo3-extensions-seo/ -->';
    }

    public function render(array &$params, $pObj): void
    {
        if (!($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            || !ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()
        ) {
            return;
        }

        $data = $this->getStructuredData();

        $params['headerData']['StructuredDataManager'] = $this->sourceComment .
            PHP_EOL . $this->buildJsonLdBlob($data);
    }

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
                        // TODO: Fix this call, protected method since v13
                        //$this->getTypoScriptFrontendController()->get_cache_timeout()
                    );
                }

                if (!empty($data)) {
                    $structuredData[$provider] = $data;
                }
            }
        }

        return $structuredData;
    }

    private function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }

    private function getStructuredDataProviderConfiguration(): array
    {
        $typoscriptService = GeneralUtility::makeInstance(TypoScriptService::class);
        $config = $typoscriptService->convertTypoScriptArrayToPlainArray(
            $this->getTypoScriptFrontendController()->config['config'] ?? []
        );

        return $config['structuredData']['providers'] ?? [];
    }

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
