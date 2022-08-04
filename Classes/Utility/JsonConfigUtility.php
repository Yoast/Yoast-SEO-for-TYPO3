<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Utility;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;

class JsonConfigUtility implements SingletonInterface
{
    protected array $config = [];

    /**
     * @param array $config
     */
    public function addConfig(array $config)
    {
        ArrayUtility::mergeRecursiveWithOverrule($this->config, $config, true, false);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return 'const YoastConfig = ' . json_encode($this->config) . ';';
    }
}
