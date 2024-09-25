<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Utility;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;

class JsonConfigUtility implements SingletonInterface
{
    /** @var array<string, mixed> */
    protected array $config = [];

    /**
     * @param array<string, mixed> $config
     */
    public function addConfig(array $config): void
    {
        ArrayUtility::mergeRecursiveWithOverrule($this->config, $config, true, false);
    }

    public function render(): string
    {
        return 'const YoastConfig = ' . json_encode($this->config) . ';';
    }
}
