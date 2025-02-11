<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class YoastEnvironmentService
{
    public function __construct(
        protected ConfigurationManagerInterface $configurationManager
    ) {}

    /**
     * Returns true if Yoast extension is in development mode. You need a webpack dev server running to load
     * JS files if not in production mode
     *
     * You can set development by using TypoScript "module.tx_yoastseo.settings.developmentMode = 1"
     *
     * @return bool
     */
    public function isDevelopmentMode(): bool
    {
        $configuration = $this->getTyposcriptConfiguration();
        return (int)($_ENV['YOAST_DEVELOPMENT_MODE'] ?? 0) === 1 || (int)($configuration['developmentMode'] ?? 0) === 1;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getTypoScriptConfiguration(): array
    {
        return $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'yoastseo'
        );
    }
}
