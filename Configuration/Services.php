<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Dashboard\Dashboard;
use YoastSeoForTypo3\YoastSeo\Widgets\PageOverviewWidget;
use YoastSeoForTypo3\YoastSeo\Widgets\Provider\OrphanedContentDataProvider;
use YoastSeoForTypo3\YoastSeo\Widgets\Provider\PagesWithoutDescriptionDataProvider;

return function (ContainerConfigurator $configurator, ContainerBuilder $containerBuilder) {
    $services = $configurator->services();

    /*if ($containerBuilder->hasDefinition(Dashboard::class)) {
        $services->set('yoast_seo.dashboard.widget.pagesWithoutMetaDescription')
            ->class(PageOverviewWidget::class)
            ->arg('$dataProvider', new Reference(PagesWithoutDescriptionDataProvider::class))
            ->arg('$view', new Reference('dashboard.views.widget'))
            ->arg('$buttonProvider', null)
            ->arg('$options', ['template' => 'Widget/PageWithoutMetaDescriptionWidget'])
            ->tag(
                'dashboard.widget',
                [
                    'identifier' => 'yoastseo-pagesWithoutMetaDescription',
                    'groupNames' => 'seo',
                    'title' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:dashboard.widget.pagesWithoutMetaDescription.title',
                    'description' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:dashboard.widget.pagesWithoutMetaDescription.description',
                    'iconIdentifier' => 'extension-yoast',
                    'height' => 'large',
                    'width' => 'medium'
                ]
            );

        $services->set('yoast_seo.dashboard.widget.orphanedContent')
            ->class(PageOverviewWidget::class)
            ->arg('$dataProvider', new Reference(OrphanedContentDataProvider::class))
            ->arg('$view', new Reference('dashboard.views.widget'))
            ->arg('$buttonProvider', null)
            ->arg('$options', ['template' => 'Widget/OrphanedContentWidget'])
            ->tag(
                'dashboard.widget',
                [
                    'identifier' => 'yoastseo-orphanedContent',
                    'groupNames' => 'seo',
                    'title' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:dashboard.widget.orphanedContent.title',
                    'description' => 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModule.xlf:dashboard.widget.orphanedContent.description',
                    'iconIdentifier' => 'extension-yoast',
                    'height' => 'large',
                    'width' => 'medium'
                ]
            );
    }*/
};
