<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Dashboard\Dashboard;
use YoastSeoForTypo3\YoastSeo\MetaTag\Generator\GeneratorInterface;
use YoastSeoForTypo3\YoastSeo\Service\Frontend\FrontendService;
use YoastSeoForTypo3\YoastSeo\Service\Frontend\FrontendServiceInterface;
use YoastSeoForTypo3\YoastSeo\Service\Frontend\LegacyFrontendService;
use YoastSeoForTypo3\YoastSeo\Service\Hmac\HmacGeneratorService;
use YoastSeoForTypo3\YoastSeo\Service\Hmac\HmacGeneratorServiceInterface;
use YoastSeoForTypo3\YoastSeo\Service\Hmac\LegacyHmacGeneratorService;
use YoastSeoForTypo3\YoastSeo\Service\StandaloneView\LegacyStandaloneViewService;
use YoastSeoForTypo3\YoastSeo\Service\StandaloneView\StandaloneViewService;
use YoastSeoForTypo3\YoastSeo\Service\StandaloneView\StandaloneViewServiceInterface;
use YoastSeoForTypo3\YoastSeo\StructuredData\StructuredDataProviderInterface;
use YoastSeoForTypo3\YoastSeo\Widgets\PageOverviewWidget;
use YoastSeoForTypo3\YoastSeo\Widgets\Provider\OrphanedContentDataProvider;

return static function (ContainerConfigurator $configurator, ContainerBuilder $containerBuilder) {
    $containerBuilder->registerForAutoconfiguration(StructuredDataProviderInterface::class)->setPublic(true);
    $containerBuilder->registerForAutoconfiguration(GeneratorInterface::class)->setPublic(true);

    $services = $configurator->services();

    if ((new Typo3Version())->getMajorVersion() === 12) {
        $services->alias(StandaloneViewServiceInterface::class, LegacyStandaloneViewService::class);
        $services->alias(HmacGeneratorServiceInterface::class, LegacyHmacGeneratorService::class);
        $services->alias(FrontendServiceInterface::class, LegacyFrontendService::class);
    } else {
        $services->alias(StandaloneViewServiceInterface::class, StandaloneViewService::class);
        $services->alias(HmacGeneratorServiceInterface::class, HmacGeneratorService::class);
        $services->alias(FrontendServiceInterface::class, FrontendService::class);
    }

    if (!$containerBuilder->hasDefinition(Dashboard::class)) {
        return;
    }

    $services->set('yoast_seo.dashboard.widget.orphanedContent')->class(PageOverviewWidget::class)->arg(
        '$dataProvider',
        new Reference(OrphanedContentDataProvider::class)
    )->arg(
        '$options',
        ['template' => 'Widget/OrphanedContentWidget']
    )->arg(
        '$view',
        new Reference(BackendViewFactory::class)
    )->tag(
        'dashboard.widget',
        [
            'identifier' => 'yoastseo-orphanedContent',
            'groupNames' => 'seo',
            'title' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:dashboard.widget.orphanedContent.title',
            'description' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:dashboard.widget.orphanedContent.description',
            'iconIdentifier' => 'extension-yoast',
            'height' => 'large',
            'width' => 'medium',
        ]
    );
};
