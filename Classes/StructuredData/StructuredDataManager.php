<?php
declare(strict_types = 1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class StructuredDataManager implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $providers = [];

    /**
     * @var string
     */
    protected $sourceComment = '';

    public function __construct()
    {
        if (YoastUtility::isPremiumInstalled()) {
            $this->sourceComment = '<!-- This site is optimized with the Yoast SEO Premium for TYPO3 plugin - https://yoast.com/typo3-extensions-seo/ -->';
        } else {
            $this->sourceComment = '<!-- This site is optimized with the Yoast SEO for TYPO3 plugin - https://yoast.com/typo3-extensions-seo/ -->';
        }
    }

    /**
     * @param string $name
     * @param StructuredDataProviderInterface $provider
     *
     * @return array
     */
    public function addProvider($name, $provider): array
    {
        $this->providers[$name] = $provider;

        return $this->providers;
    }

    /**
     * @param $name
     *
     * @return array
     */
    public function removeProvider($name): array
    {
        unset($this->providers[$name]);

        return $this->providers;
    }

    /**
     * @param array $params
     * @param object $pObj
     */
    public function render(&$params, $pObj): void
    {
        $this->data = $this->getDataFromProviders();

        $params['headerData']['StructuredDataManager'] = $this->sourceComment . PHP_EOL . $this->buildJsonLdBlob();
    }

    protected function getDataFromProviders(): array
    {
        $data = [];

        foreach ($this->providers as $providerClass) {
            $provider = GeneralUtility::makeInstance($providerClass);
            if ($provider instanceof StructuredDataProviderInterface) {
                $data[] = $provider->getData();
            }
        }

        return $data;
    }
    /**
     * @return string
     */
    protected function buildJsonLdBlob(): string
    {
        return '<script type="application/ld+json">' . json_encode($this->data) . '</script>';
    }
}