<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Dashboard\Dashboard;
use YoastSeoForTypo3\YoastSeo\MetaTag\Generator\GeneratorInterface;
use YoastSeoForTypo3\YoastSeo\StructuredData\StructuredDataProviderInterface;
use YoastSeoForTypo3\YoastSeo\Widgets\PageOverviewWidget;
use YoastSeoForTypo3\YoastSeo\Widgets\Provider\OrphanedContentDataProvider;

return static function (ContainerConfigurator $configurator, ContainerBuilder $containerBuilder) {
    $containerBuilder->registerForAutoconfiguration(StructuredDataProviderInterface::class)->setPublic(true);
    $containerBuilder->registerForAutoconfiguration(GeneratorInterface::class)->setPublic(true);

    $services = $configurator->services();

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
